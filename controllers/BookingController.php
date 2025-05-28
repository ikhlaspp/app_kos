<?php

class BookingController extends BaseController {
    private KosModel $kosModel;
    private BookingModel $bookingModel;
    private PaymentModel $paymentModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->kosModel = new KosModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
        $this->paymentModel = new PaymentModel($this->pdo);
    }

    public function pesan($kos_id = null): void {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage("Anda harus login terlebih dahulu untuk melakukan pemesanan.", "error");
            $targetRedirect = 'booking/pesan/' . ($kos_id ?? '');
            $this->redirect('auth/login?redirect_to=' . urlencode($targetRedirect));
            return;
        }

        if ($kos_id === null) {
            $this->setFlashMessage("ID Kos tidak disertakan untuk pemesanan.", "error");
            $this->redirect('kos/daftar');
            return;
        }
        
        $kos_id_filtered = filter_var($kos_id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) {
            $this->setFlashMessage("Format ID Kos tidak valid.", "error");
            $this->redirect('kos/daftar');
            return;
        }

        $kos = $this->kosModel->getKosById($kos_id_filtered);

        if ($kos === null || $kos === false) {
            $this->setFlashMessage("Kos tidak ditemukan.", "error");
            $this->redirect('kos/daftar');
            return;
        }
        if ($kos['status_kos'] === 'maintenance') {
            $this->setFlashMessage("Kos ini sedang dalam perbaikan dan tidak bisa dipesan.", "info");
            $this->redirect('kos/detail/' . $kos_id_filtered);
            return;
        }
        if (($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 && $kos['status_kos'] !== 'maintenance') {
            $this->setFlashMessage("Maaf, semua kamar untuk kos ini sudah terpesan.", "info");
            $this->redirect('kos/detail/' . $kos_id_filtered);
            return;
        }

        $pageTitle = "Booking & Pembayaran: " . htmlspecialchars($kos['nama_kos']);
        $viewName = 'booking/form_pemesanan'; 
        $dataToView = ['kos' => $kos, 'input' => [] ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataToView['input'] = $_POST; 
            
            $tanggal_mulai_str = isset($_POST['tanggal_mulai']) ? trim($_POST['tanggal_mulai']) : '';
            $nama_pembayar     = isset($_POST['nama_pembayar']) ? trim(strip_tags($_POST['nama_pembayar'])) : '';
            $kontak_pembayar   = isset($_POST['kontak_pembayar']) ? trim(strip_tags($_POST['kontak_pembayar'])) : '';
            $metode_pembayaran = isset($_POST['metode_pembayaran']) ? trim(strip_tags($_POST['metode_pembayaran'])) : '';
            
            $durasi_sewa_bulan = filter_input(INPUT_POST, 'durasi_sewa', FILTER_VALIDATE_INT);

            $errors = [];
            if (empty($tanggal_mulai_str)) { $errors[] = "Tanggal mulai sewa wajib diisi."; }
            if ($durasi_sewa_bulan === false || $durasi_sewa_bulan <= 0) { $errors[] = "Durasi sewa tidak valid (minimal 1 bulan)."; }
            if (empty($nama_pembayar)) { $errors[] = "Nama pembayar wajib diisi."; }
            if (empty($kontak_pembayar)) { $errors[] = "Kontak pembayar wajib diisi."; }
            if (empty($metode_pembayaran)) { $errors[] = "Metode pembayaran wajib dipilih."; }

            $total_harga = 0;
            
            $tanggal_selesai_str = '';
            $durasi_sewa_text = '';

            if (empty($errors)) {
                try {
                    $tanggal_mulai_obj = new DateTime($tanggal_mulai_str);
                    $today = new DateTime('today');
                    if ($tanggal_mulai_obj < $today) {
                        $errors[] = "Tanggal mulai sewa tidak boleh tanggal yang sudah berlalu.";
                    } else {
                        if ($durasi_sewa_bulan > 0 && isset($kos['harga_per_bulan'])) {
                            $total_harga = $kos['harga_per_bulan'] * $durasi_sewa_bulan;
                            $pajak = $total_harga * 0.10;
                            $total_harga = $total_harga + $pajak;
                            $tanggal_selesai_obj = (clone $tanggal_mulai_obj)->add(new DateInterval("P{$durasi_sewa_bulan}M"));
                            $tanggal_selesai_str = $tanggal_selesai_obj->format('Y-m-d');
                            $durasi_sewa_text = "{$durasi_sewa_bulan} bulan";
                        }
                    }
                } catch (Exception $e) {
                    if (!in_array("Tanggal mulai sewa wajib diisi.", $errors) && !in_array("Tanggal mulai sewa tidak boleh tanggal yang sudah berlalu.", $errors) ) {
                        $errors[] = "Format tanggal mulai tidak valid.";
                    }
                    $tanggal_selesai_str = ''; 
                }
            }
            
            if (empty($errors)) { // Hanya cek ketersediaan jika tidak ada error input dasar
                $kosSaatIni = $this->kosModel->getKosById($kos['id']);
                if (!$kosSaatIni || ($kosSaatIni['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kosSaatIni['status_kos'] !== 'available') {
                    $this->setFlashMessage("Maaf, status ketersediaan kos baru saja berubah. Kemungkinan kamar terakhir baru saja dipesan.", "error");
                    $this->redirect('kos/detail/' . $kos['id']);
                    return;
                }
            }

            if (!empty($errors)) {
                $this->setFlashMessage(implode("<br>", $errors), "error");
                $this->loadView($viewName, $dataToView, $pageTitle);
                return;
            }

            $userId = (int)$_SESSION['user_id']; 
            $this->pdo->beginTransaction();

            $bookingId = $this->bookingModel->createBooking($userId, $kos['id'], $tanggal_mulai_str, $tanggal_selesai_str, $durasi_sewa_text, $total_harga, 'pending'); // STATUS AWAL PENDING

            if ($bookingId) {
                // Nominal bayar otomatis menggunakan total harga yang dihitung
                
                $nominal_bayar = $total_harga;
                $paymentId = $this->paymentModel->createPayment((int)$bookingId, $metode_pembayaran, $nominal_bayar, 'paid'); // Asumsi pembayaran langsung 'paid'

                if ($paymentId) {
                    // Jumlah kamar TIDAK dikurangi di sini, akan dikurangi oleh admin saat konfirmasi
                    $this->pdo->commit(); 
                    
                    $pesan_sukses = "Pemesanan Anda dengan ID: <strong>{$bookingId}</strong> telah berhasil dibuat dan sedang menunggu konfirmasi dari admin.<br>" .
                                    "Anda akan segera dihubungi setelah pesanan Anda diproses. <br>" .
                                    "Kos: " . htmlspecialchars($kos['nama_kos']) . "<br>" .
                                    "Durasi: {$durasi_sewa_text} (Mulai: {$tanggal_mulai_str} s/d {$tanggal_selesai_str})<br>" .
                                    "Total Tagihan: Rp " . number_format($total_harga, 0, ',', '.');
                    $this->setFlashMessage($pesan_sukses, "success");
                    $this->redirect('user/dashboard'); 
                } else {
                    $this->pdo->rollBack(); 
                    $this->setFlashMessage("Pemesanan berhasil dibuat (ID: {$bookingId}), tetapi pencatatan pembayaran gagal. Silakan coba lagi atau hubungi admin.", "error");
                    // $this->redirect('kos/detail/' . $kos['id']); // Bisa juga kembali ke form dengan data
                    $this->loadView($viewName, $dataToView, $pageTitle); // Kembali ke form dengan data input
                }
            } else {
                $this->pdo->rollBack(); 
                $this->setFlashMessage("Gagal membuat pemesanan. Terjadi kesalahan pada server.", "error");
                $this->loadView($viewName, $dataToView, $pageTitle);
            }

        } else {
            $this->loadView($viewName, $dataToView, $pageTitle);
        }
    }
}
?>