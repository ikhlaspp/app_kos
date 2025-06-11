<?php

class BookingController extends BaseController {
    private KosModel $kosModel;
    private BookingModel $bookingModel;
    private PaymentModel $paymentModel;
    private VoucherModel $voucherModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->kosModel = new KosModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
        $this->paymentModel = new PaymentModel($this->pdo);
        $this->voucherModel = new VoucherModel($this->pdo);
    }

    public function pesan($kos_id = null): void {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage("Anda harus login terlebih dahulu untuk melakukan pemesanan.", "error");
            $targetRedirect = 'booking/pesan/' . ($kos_id ?? '');
            $this->redirect('auth/login?redirect_to=' . urlencode($targetRedirect));
            return;
        }

        $userId = $_SESSION['user_id'];

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
        
        $claimedVouchers = $this->voucherModel->getUserClaimedVouchers($userId);
        $usableVouchers = array_filter($claimedVouchers, function($voucher) {
            return $voucher['status'] === 'claimed'
                   && strtotime($voucher['expiration_date']) > time()
                   && $voucher['times_used'] < $voucher['usage_limit_per_user'];
        });

        $dataToView = [
            'kos' => $kos,
            'input' => [],
            'usableVouchers' => $usableVouchers
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataToView['input'] = $_POST; 
            
            $tanggal_mulai_str = $this->getInputPost('tanggal_mulai');
            $nama_pembayar     = $this->getInputPost('nama_pembayar', null, FILTER_SANITIZE_SPECIAL_CHARS);
            $kontak_pembayar   = $this->getInputPost('kontak_pembayar', null, FILTER_SANITIZE_SPECIAL_CHARS);
            $metode_pembayaran = $this->getInputPost('metode_pembayaran', null, FILTER_SANITIZE_SPECIAL_CHARS);
            $durasi_sewa_bulan = $this->getInputPost('durasi_sewa', null, FILTER_VALIDATE_INT);
            $voucherId         = $this->getInputPost('voucher_id', null, FILTER_VALIDATE_INT);

            $errors = [];
            if (empty($tanggal_mulai_str)) { $errors[] = "Tanggal mulai sewa wajib diisi."; }
            if ($durasi_sewa_bulan === false || $durasi_sewa_bulan <= 0) { $errors[] = "Durasi sewa tidak valid (minimal 1 bulan)."; }
            if (empty($nama_pembayar)) { $errors[] = "Nama pembayar wajib diisi."; }
            if (empty($kontak_pembayar)) { $errors[] = "Kontak pembayar wajib diisi."; }
            if (empty($metode_pembayaran)) { $errors[] = "Metode pembayaran wajib dipilih."; }

            $total_harga = 0;
            $tanggal_selesai_str = '';
            $durasi_sewa_text = '';
            $appliedVoucherId = null;
            $discountAmount = 0;

            if (empty($errors)) {
                try {
                    $tanggal_mulai_obj = new DateTime($tanggal_mulai_str);
                    $today = new DateTime('today');
                    if ($tanggal_mulai_obj < $today) {
                        $errors[] = "Tanggal mulai sewa tidak boleh tanggal yang sudah berlalu.";
                    } else {
                        if ($durasi_sewa_bulan > 0 && isset($kos['harga_per_bulan'])) {
                            $base_rent_price = $kos['harga_per_bulan'] * $durasi_sewa_bulan;
                            $pajak = $base_rent_price * 0.10;
                            $total_harga_before_discount = $base_rent_price + $pajak;

                            $tanggal_selesai_obj = (clone $tanggal_mulai_obj)->add(new DateInterval("P{$durasi_sewa_bulan}M"));
                            $tanggal_selesai_str = $tanggal_selesai_obj->format('Y-m-d');
                            $durasi_sewa_text = "{$durasi_sewa_bulan} bulan";

                            if ($voucherId) {
                                $voucher = $this->voucherModel->getVoucherById($voucherId);

                                if (!$voucher || $voucher['is_active'] == 0 || strtotime($voucher['expiration_date']) < time()) {
                                    $errors[] = "Voucher tidak valid atau sudah kadaluarsa.";
                                } else {
                                    $userVoucher = $this->voucherModel->getUserVoucher($userId, $voucher['id']);
                                    if (!$userVoucher || $userVoucher['status'] !== 'claimed' || $userVoucher['times_used'] >= $voucher['usage_limit_per_user']) {
                                        $errors[] = "Voucher ini belum diaktifkan/klaim penuh atau tidak dapat digunakan lagi.";
                                    } elseif ($voucher['total_usage_limit'] !== null && $voucher['current_total_uses'] >= $voucher['total_usage_limit']) {
                                        $errors[] = "Voucher ini sudah mencapai batas penggunaan total.";
                                    } elseif ($voucher['min_transaction_amount'] !== null && $total_harga_before_discount < $voucher['min_transaction_amount']) {
                                        $errors[] = "Voucher memerlukan minimal transaksi Rp " . number_format($voucher['min_transaction_amount'], 0, ',', '.') . " untuk berlaku.";
                                    } else {
                                        if ($voucher['type'] === 'percentage') {
                                            $discountAmount = $total_harga_before_discount * ($voucher['value'] / 100);
                                            if ($voucher['max_discount_amount'] !== null && $discountAmount > $voucher['max_discount_amount']) {
                                                $discountAmount = $voucher['max_discount_amount'];
                                            }
                                        } elseif ($voucher['type'] === 'fixed_amount') {
                                            $discountAmount = $voucher['value'];
                                        }
                                        $total_harga_after_discount = $total_harga_before_discount - $discountAmount;
                                        if ($total_harga_after_discount < 0) $total_harga_after_discount = 0;

                                        $total_harga = $total_harga_after_discount;
                                        $appliedVoucherId = $voucher['id'];
                                    }
                                }
                            }
                            if (empty($appliedVoucherId)) {
                                $total_harga = $total_harga_before_discount;
                            }
                            if ($total_harga < 0) $total_harga = 0;

                        }
                    }
                } catch (Exception $e) {
                    if (!in_array("Tanggal mulai sewa wajib diisi.", $errors) && !in_array("Tanggal mulai sewa tidak boleh tanggal yang sudah berlalu.", "error")) {
                        $errors[] = "Format tanggal mulai tidak valid.";
                    }
                    $tanggal_selesai_str = '';
                }
            }
            
            if (empty($errors)) {
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
            // --- COMMENTED OUT: Transaction Handling ---
            /*
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->pdo->beginTransaction();
            */
            // --- END COMMENTED OUT ---

            try {
                $bookingId = $this->bookingModel->createBooking($userId, $kos['id'], $tanggal_mulai_str, $tanggal_selesai_str, $durasi_sewa_text, $total_harga, 'pending', $appliedVoucherId ?? null);

                if ($bookingId) {
                    if ($appliedVoucherId) {
                        if (!$this->voucherModel->markVoucherAsUsed($userId, $appliedVoucherId)) {
                            throw new Exception("Gagal menandai voucher sebagai telah digunakan.");
                        }
                    }

                    $nominal_bayar = $total_harga;
                    $paymentId = $this->paymentModel->createPayment((int)$bookingId, $metode_pembayaran, $nominal_bayar, 'paid');

                    if ($paymentId) {
                        // --- COMMENTED OUT: Transaction Commit ---
                        // $this->pdo->commit();
                        // --- END COMMENTED OUT ---
                        
                        $pesan_sukses = "Pemesanan Anda dengan ID: <strong>{$bookingId}</strong> telah berhasil dibuat dan sedang menunggu konfirmasi dari admin.<br>" .
                                        "Anda akan segera dihubungi setelah pesanan Anda diproses. <br>" .
                                        "Kos: " . htmlspecialchars($kos['nama_kos']) . "<br>" .
                                        "Durasi: {$durasi_sewa_text} (Mulai: {$tanggal_mulai_str} s/d {$tanggal_selesai_str})<br>" .
                                        "Total Tagihan: Rp " . number_format($total_harga, 0, ',', '.');
                        if ($appliedVoucherId && $discountAmount > 0) {
                            $pesan_sukses .= "<br>Diskon voucher berhasil diterapkan sebesar Rp " . number_format($discountAmount, 0, ',', '.');
                        }
                        $this->setFlashMessage($pesan_sukses, "success");
                        $this->redirect('user/dashboard');
                    } else {
                        throw new Exception("Pencatatan pembayaran gagal.");
                    }
                } else {
                    throw new Exception("Gagal membuat pemesanan di database.");
                }
            } catch (Exception $e) {
                // --- COMMENTED OUT: Transaction Rollback ---
                /*
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                */
                // --- END COMMENTED OUT ---
                $this->setFlashMessage("Pemesanan gagal: " . $e->getMessage(), "error");
                error_log("BookingController::pesan PDOException/Exception: " . $e->getMessage());
                $this->loadView($viewName, $dataToView, $pageTitle);
            }

        } else { // GET request for the form
            $this->loadView($viewName, $dataToView, $pageTitle);
        }
    }
}