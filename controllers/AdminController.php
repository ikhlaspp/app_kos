<?php

class AdminController extends BaseController {
    private KosModel $kosModel;
    private UserModel $userModel;
    private BookingModel $bookingModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->_checkAdmin(); 
        $this->kosModel = new KosModel($this->pdo);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
    }

    private function _checkAdmin(): void {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            $this->setFlashMessage("Anda tidak memiliki hak akses ke halaman admin.", "error");
            if (isset($_SESSION['user_id'])) {
                $this->redirect(''); 
            } else {
                $this->redirect('auth/login'); 
            }
            exit;
        }
    }

    public function dashboard(): void {
        $pageTitle = "Admin Dashboard";
        $this->loadView('admin/dashboard', [], $pageTitle);
    }

    // --- KOS CRUD ---
    public function kos(): void { 
        $pageTitle = "Kelola Data Kos";
        $daftarKos = $this->kosModel->getAllKos('id', 'ASC');
        $this->loadView('admin/kos/list', ['daftarKos' => $daftarKos], $pageTitle);
    }
    public function kosCreate(): void { 
        $pageTitle = "Tambah Kos Baru";
        $viewName = 'admin/kos/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosCreate',
            'kos' => null, 'mode' => 'create', 'pageTitle' => $pageTitle
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST; // Ambil semua data post
            $errors = [];
            if (empty($data['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            // ... validasi lain ...
            $dataForView['kos'] = $data; // Kirim input kembali jika error
            if (empty($errors)) {
                $newKosId = $this->kosModel->createKos($data);
                if ($newKosId) {
                    $this->setFlashMessage("Data kos baru berhasil ditambahkan.", "success");
                    $this->redirect('admin/kos'); return;
                } else { $this->setFlashMessage("Gagal menambahkan data kos.", "error"); }
            } else { $this->setFlashMessage(implode("<br>", $errors), "error"); }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }
    public function kosEdit($id = null): void { 
        if ($id === null) { $this->redirect('admin/kos'); return; }
        $kos = $this->kosModel->getKosById((int)$id);
        if (!$kos) { $this->setFlashMessage("Kos tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }

        $pageTitle = "Edit Data Kos: " . htmlspecialchars($kos['nama_kos']);
        $viewName = 'admin/kos/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id'],
            'kos' => $kos, 'mode' => 'edit', 'pageTitle' => $pageTitle
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateData = $_POST; // Ambil semua data post
            $errors = [];
             if (empty($updateData['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            // ... validasi lain untuk updateData ...
            $dataForView['kos'] = array_merge($kos, $updateData); // Update data untuk form jika error
            if (empty($errors)) {
                if ($this->kosModel->updateKos((int)$id, $updateData)) {
                    $this->setFlashMessage("Data kos berhasil diperbarui.", "success");
                    $this->redirect('admin/kos'); return;
                } else { $this->setFlashMessage("Gagal memperbarui data kos atau tidak ada perubahan.", "error");}
            } else { $this->setFlashMessage(implode("<br>", $errors), "error");}
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }
    public function kosDelete($id = null): void { 
        if ($id === null) { $this->redirect('admin/kos'); return; }
        $kos = $this->kosModel->getKosById((int)$id);
        if (!$kos) { $this->setFlashMessage("Kos tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->kosModel->deleteKos((int)$id)) {
                $this->setFlashMessage("Data kos '" . htmlspecialchars($kos['nama_kos']) . "' berhasil dihapus.", "success");
            } else { $this->setFlashMessage("Gagal menghapus data kos.", "error");}
            $this->redirect('admin/kos'); return;
        }
        $this->loadView('admin/kos/delete_confirm', ['kos' => $kos, 'pageTitle' => 'Konfirmasi Hapus Kos', 'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']], 'Konfirmasi Hapus Kos');
    }

    // --- USER CRUD (List dan Edit oleh Admin) ---
    public function users(): void { 
        $pageTitle = "Kelola Pengguna";
        $daftarPengguna = $this->userModel->getAllUsers();
        $this->loadView('admin/users/list', ['daftarPengguna' => $daftarPengguna], $pageTitle);
    }
    public function userEdit($id = null): void { 
        if ($id === null) { $this->redirect('admin/users'); return; }
        $user = $this->userModel->getUserById((int)$id);
        if (!$user) { $this->setFlashMessage("Pengguna tidak ditemukan.", "error"); $this->redirect('admin/users'); return; }
        $pageTitle = "Edit Pengguna: " . htmlspecialchars($user['nama']);
        $viewName = 'admin/users/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/userEdit/' . $user['id'],
            'user' => $user, 'pageTitle' => $pageTitle
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim(strip_tags($_POST['nama'] ?? ''));
            $no_telepon = !empty(trim(strip_tags($_POST['no_telepon'] ?? ''))) ? trim(strip_tags($_POST['no_telepon'])) : null;
            $alamat = !empty(trim(strip_tags($_POST['alamat'] ?? ''))) ? trim(strip_tags($_POST['alamat'])) : null;
            $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] == '1';
            $errors = [];
            if (empty($nama)) $errors[] = "Nama wajib diisi.";
            $dataForView['user'] = array_merge($user, $_POST);
            if (empty($errors)) {
                if ($this->userModel->updateUserByAdmin((int)$id, $nama, $no_telepon, $alamat, $is_admin)) {
                    $this->setFlashMessage("Data pengguna berhasil diperbarui.", "success");
                    $this->redirect('admin/users'); return;
                } else { $this->setFlashMessage("Gagal memperbarui data pengguna.", "error");}
            } else { $this->setFlashMessage(implode("<br>", $errors), "error");}
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }


    // --- BOOKING MANAGEMENT ---
    public function bookings(): void {
        $pageTitle = "Kelola Pemesanan";
        $daftarBooking = $this->bookingModel->getAllBookings();
        $data = ['daftarBooking' => $daftarBooking];
        $this->loadView('admin/bookings/list', $data, $pageTitle);
    }

    public function bookingDetail($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }

        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        if (!$booking) {
            $this->setFlashMessage("Detail booking dengan ID {$id} tidak ditemukan.", "error");
            $this->redirect('admin/bookings');
            return;
        }
        $pageTitle = "Detail Pemesanan #" . htmlspecialchars($booking['booking_id_val']);
        $this->loadView('admin/bookings/detail', ['booking' => $booking], $pageTitle);
    }

    public function bookingConfirm($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }

        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        if (!$booking || $booking['status_pemesanan'] !== 'pending') {
            $this->setFlashMessage("Booking tidak ditemukan atau statusnya bukan 'pending' sehingga tidak bisa dikonfirmasi.", "warning");
            $this->redirect('admin/bookings');
            return;
        }

        $kos = $this->kosModel->getKosById($booking['kos_id']);
        if (!$kos || ($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kos['status_kos'] !== 'available') {
             $this->setFlashMessage("Tidak dapat mengonfirmasi: Kamar untuk kos '" . htmlspecialchars($kos['nama_kos'] ?? 'N/A') . "' sudah habis atau kos tidak tersedia.", "error");
             $this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected'); // Otomatis reject jika kamar habis saat mau konfirm
             $this->redirect('admin/bookings');
             return;
        }
        
        $this->pdo->beginTransaction();
        try {
            $kamarDecremented = $this->kosModel->decrementKamarTersedia($booking['kos_id']);
            if (!$kamarDecremented) {
                throw new Exception("Gagal mengurangi jumlah kamar. Mungkin kamar terakhir baru saja dipesan orang lain.");
            }
            $bookingUpdated = $this->bookingModel->updateBookingStatus($booking_id_filtered, 'confirmed');
            if (!$bookingUpdated) {
                throw new Exception("Gagal mengupdate status booking menjadi 'confirmed'.");
            }
            
            $this->pdo->commit();
            $this->setFlashMessage("Booking ID {$booking_id_filtered} berhasil dikonfirmasi. Jumlah kamar telah diperbarui.", "success");
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->setFlashMessage("Gagal mengonfirmasi booking: " . $e->getMessage(), "error");
        }
        $this->redirect('admin/bookings');
    }

    public function bookingReject($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }

        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        // Hanya bisa reject yang pending. Jika sudah confirmed dan mau dibatalkan, itu proses lain (cancel).
        if (!$booking || $booking['status_pemesanan'] !== 'pending') {
            $this->setFlashMessage("Booking tidak ditemukan atau statusnya bukan 'pending' sehingga tidak bisa ditolak.", "warning");
            $this->redirect('admin/bookings');
            return;
        }
        
        // Tidak ada perubahan pada jumlah kamar jika booking ditolak sebelum dikonfirmasi
        if ($this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected')) {
            $this->setFlashMessage("Booking ID {$booking_id_filtered} berhasil ditolak.", "success");
        } else {
            $this->setFlashMessage("Gagal menolak booking ID {$booking_id_filtered}.", "error");
        }
        $this->redirect('admin/bookings');
    }
}
?>