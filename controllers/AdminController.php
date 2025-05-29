<?php
// File: nama_proyek_kos/controllers/AdminController.php

class AdminController extends BaseController {
    private KosModel $kosModel;
    private UserModel $userModel;
    private BookingModel $bookingModel;
    private LogAuditModel $logAuditModel; // Pastikan class LogAuditModel sudah ada dan di-autoload

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->_checkAdmin(); 
        
        $this->kosModel = new KosModel($this->pdo);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
        if (class_exists('LogAuditModel')) { // Cek jika class LogAuditModel ada sebelum membuat instance
           $this->logAuditModel = new LogAuditModel($this->pdo);
        } else {
            // Fallback atau error jika LogAuditModel tidak ditemukan, agar tidak fatal error
            // error_log("Peringatan: Class LogAuditModel tidak ditemukan. Fitur log audit tidak akan aktif.");
            // Anda bisa membuat dummy LogAuditModel jika ingin menghindari error jika file tidak ada.
            // Untuk sekarang, kita biarkan, tapi pastikan file LogAuditModel.php ada.
        }
    }

     protected function loadAdminView(string $viewName, array $data = [], ?string $pageTitle = null): void {
        // Data yang akan di-extract di layout_admin.php dan view spesifik
        $data['appConfig'] = $this->appConfig; // Pastikan appConfig tersedia untuk layout
        $data['pageTitle'] = $pageTitle ?? ($data['pageTitle'] ?? 'Admin Panel'); // pageTitle untuk layout
        $data['contentView'] = 'admin/' . $viewName; // Path ke view konten spesifik relatif dari folder views/

        // Path ke file layout admin
        $layoutAdminPath = $this->appConfig['VIEWS_PATH'] . 'admin/layout_admin.php';

        if (file_exists($layoutAdminPath)) {
            extract($data); // Extract semua data agar tersedia di layout_admin.php
            require_once $layoutAdminPath;
        } else {
            // Fallback jika layout admin tidak ditemukan
            echo "Error: File layout admin tidak ditemukan.";
            // Mungkin redirect ke halaman error atau dashboard standar
        }
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
        $pageTitle = "Admin Dashboard - Ringkasan";
        $totalUsers = $this->userModel->countTotalUsers() ?? 0;
        $totalKos = $this->kosModel->countTotalKos() ?? 0;
        $pendingBookings = $this->bookingModel->countPendingBookings() ?? 0;
        $recentConfirmedBookings = $this->bookingModel->getRecentConfirmedBookings(5) ?? [];
        
        $recentLogs = [];
        if (isset($this->logAuditModel)) { // Hanya panggil jika modelnya ada
            $recentLogs = $this->logAuditModel->getRecentLogs(7) ?? [];
        }

        $data = [
            'totalUsers' => $totalUsers,
            'totalKos' => $totalKos,
            'pendingBookings' => $pendingBookings,
            'recentConfirmedBookings' => $recentConfirmedBookings,
            'recentLogs' => $recentLogs,
        ];
        $this->loadAdminView('dashboard_summary', $data, $pageTitle); 
    }

    // --- KOS CRUD ---
    public function kos(): void { 
        $pageTitle = "Manajemen Data Kos";
        $daftarKos = $this->kosModel->getAllKos('id', 'ASC');
        $this->loadAdminView('kos/list', ['daftarKos' => $daftarKos], $pageTitle);
    }
    
    private function _handleImageUploads(int $kosId, array $filesData, string $uploadDirFs): array {
        $uploadedImageInfo = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!is_dir($uploadDirFs)) {
            if (!mkdir($uploadDirFs, 0775, true)) {
                $this->setFlashMessage("Error: Gagal membuat direktori upload ({$uploadDirFs}). Periksa izin.", "error");
                return $uploadedImageInfo;
            }
        }
        if (!is_writable($uploadDirFs)) {
             $this->setFlashMessage("Error: Direktori upload ({$uploadDirFs}) tidak dapat ditulis. Periksa izin.", "error");
             return $uploadedImageInfo;
        }

        if (isset($filesData['name']) && is_array($filesData['name'])) {
            foreach ($filesData['name'] as $key => $name) {
                if (empty($name) || $filesData['error'][$key] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($filesData['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $filesData['tmp_name'][$key];
                    $originalName = basename($name);
                    $fileType = $filesData['type'][$key];
                    $fileSize = $filesData['size'][$key];

                    if (!in_array($fileType, $allowedTypes)) {
                        $this->setFlashMessage("Error Upload: Tipe file '{$originalName}' tidak diizinkan (hanya JPG, PNG, GIF, WEBP).", "error"); continue; 
                    }
                    if ($fileSize > $maxSize) {
                        $this->setFlashMessage("Error Upload: Ukuran file '{$originalName}' terlalu besar (maks 5MB).", "error"); continue;
                    }
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $safeOriginalName = preg_replace("/[^a-zA-Z0-9._-]/", "", pathinfo($originalName, PATHINFO_FILENAME));
                    $uniqueFileName = uniqid($safeOriginalName . '_', true) . '.' . $extension;
                    $destinationPathFs = rtrim($uploadDirFs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $uniqueFileName;
                    $relativePathForDb = 'kos_images/' . $uniqueFileName; 
                    if (move_uploaded_file($tmpName, $destinationPathFs)) {
                        if ($this->kosModel->addGambarKos($kosId, $originalName, $relativePathForDb)) {
                            $uploadedImageInfo[] = ['name' => $originalName, 'path' => $relativePathForDb];
                        } else {
                            $this->setFlashMessage("Error DB: Gagal menyimpan info gambar '{$originalName}'.", "error");
                            if (file_exists($destinationPathFs)) unlink($destinationPathFs); 
                        }
                    } else {
                        $uploadError = error_get_last();
                        $this->setFlashMessage("Error Sistem: Gagal memindahkan file '{$originalName}'. " . ($uploadError['message'] ?? 'Periksa izin atau path.'), "error");
                    }
                } elseif ($filesData['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                    $this->setFlashMessage("Error Upload File '{$name}': Kode PHP " . $filesData['error'][$key], "error");
                }
            }
        }
        return $uploadedImageInfo;
    }

    public function kosCreate(): void { 
        $pageTitle = "Tambah Kos Baru";
        $viewName = 'admin/kos/form';
        $dataForView = ['formAction' => $this->appConfig['BASE_URL'] . 'admin/kosCreate', 'kos' => null, 'mode' => 'create', 'pageTitle' => $pageTitle];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kosDataInput = [
                'nama_kos' => trim(strip_tags($_POST['nama_kos'] ?? '')),
                'alamat' => trim(strip_tags($_POST['alamat'] ?? '')),
                'deskripsi' => trim(strip_tags($_POST['deskripsi'] ?? '')),
                'harga_per_bulan' => filter_input(INPUT_POST, 'harga_per_bulan', FILTER_VALIDATE_FLOAT),
                'fasilitas_kos' => trim(strip_tags($_POST['fasilitas_kos'] ?? '')),
                'jumlah_kamar_total' => filter_input(INPUT_POST, 'jumlah_kamar_total', FILTER_VALIDATE_INT),
                'pemilik_id' => (int)($_SESSION['user_id'] ?? null) // Admin yang membuat adalah pemilik awal, atau tambahkan field di form
            ];
            $errors = [];
            if (empty($kosDataInput['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if (empty($kosDataInput['alamat'])) $errors[] = "Alamat wajib diisi.";
            if ($kosDataInput['harga_per_bulan'] === null || $kosDataInput['harga_per_bulan'] === false || $kosDataInput['harga_per_bulan'] <= 0) $errors[] = "Harga per bulan tidak valid.";
            if ($kosDataInput['jumlah_kamar_total'] === null || $kosDataInput['jumlah_kamar_total'] === false || $kosDataInput['jumlah_kamar_total'] < 0) {
                $errors[] = "Jumlah kamar total tidak valid (minimal 0).";
            }
            
            $dataForView['kos'] = $_POST; 
            if (empty($errors)) {
                $newKosId = $this->kosModel->createKos($kosDataInput);
                if ($newKosId) {
                    $newKosIdCasted = (int)$newKosId;
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($newKosIdCasted, $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                    if (isset($this->logAuditModel)) {
                        $this->logAuditModel->addLog("Admin membuat kos baru: " . htmlspecialchars($kosDataInput['nama_kos']), $_SESSION['user_id'], json_encode(['kos_id' => $newKosIdCasted]));
                    }
                    $this->setFlashMessage("Data kos baru berhasil ditambahkan.", "success");
                    $this->redirect('admin/kos'); 
                    return; // Penting: Hentikan eksekusi setelah redirect
                } else { 
                    $this->setFlashMessage("Gagal menambahkan data kos ke database.", "error");
                }
            } else { 
                $this->setFlashMessage(implode("<br>", $errors), "error");
            }
        }
        $this->loadAdminView('kos/form', $dataForView, $pageTitle);
    }

    public function kosEdit($id = null): void { 
        if ($id === null) { $this->setFlashMessage("ID Kos tidak ada.", "error"); $this->redirect('admin/kos'); return; }
        $kos_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) { $this->setFlashMessage("ID Kos tidak valid.", "error"); $this->redirect('admin/kos'); return; }
        $kos = $this->kosModel->getKosById($kos_id_filtered);
        if (!$kos) { $this->setFlashMessage("Kos dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }

        $pageTitle = "Edit Data Kos: " . htmlspecialchars($kos['nama_kos']);
        $viewName = 'admin/kos/form';
        $dataForView = ['formAction' => $this->appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id'], 'kos' => $kos, 'mode' => 'edit', 'pageTitle' => $pageTitle];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateData = [
                 'nama_kos' => trim(strip_tags($_POST['nama_kos'] ?? '')),
                 'alamat' => trim(strip_tags($_POST['alamat'] ?? '')),
                 'deskripsi' => trim(strip_tags($_POST['deskripsi'] ?? '')),
                 'harga_per_bulan' => filter_input(INPUT_POST, 'harga_per_bulan', FILTER_VALIDATE_FLOAT),
                 'fasilitas_kos' => trim(strip_tags($_POST['fasilitas_kos'] ?? '')),
                 'jumlah_kamar_total' => filter_input(INPUT_POST, 'jumlah_kamar_total', FILTER_VALIDATE_INT),
                 'jumlah_kamar_tersedia' => filter_input(INPUT_POST, 'jumlah_kamar_tersedia', FILTER_VALIDATE_INT),
                 'status_kos' => trim(strip_tags($_POST['status_kos'] ?? $kos['status_kos'])),
                 'pemilik_id' => filter_input(INPUT_POST, 'pemilik_id', FILTER_VALIDATE_INT) ?? $kos['pemilik_id'],
            ];
            $errors = [];
            if (empty($updateData['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if ($updateData['harga_per_bulan'] === null || $updateData['harga_per_bulan'] === false || $updateData['harga_per_bulan'] <=0) $errors[] = "Harga tidak valid.";
            // ... (Validasi lainnya)
            
            $dataForView['kos'] = array_merge($kos, $updateData); 
            if (empty($errors)) {
                if ($this->kosModel->updateKos($kos['id'], $updateData)) {
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($kos['id'], $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                     if (isset($this->logAuditModel)) {
                        $this->logAuditModel->addLog("Admin mengedit kos ID: {$kos['id']}", $_SESSION['user_id']);
                    }
                    $this->setFlashMessage("Data kos berhasil diperbarui.", "success");
                    $this->redirect('admin/kosEdit/' . $kos['id']); return;
                } else { 
                    if (empty($_SESSION['flash_message'])) { $this->setFlashMessage("Tidak ada perubahan data atau gagal update.", "info"); }
                }
            } else { $this->setFlashMessage(implode("<br>", $errors), "error"); }
        }
         $this->loadAdminView('kos/form', $dataForView, $pageTitle);
    }
    
    public function kosDeleteGambar($gambar_id = null, $kos_id = null): void {
        if ($gambar_id === null || $kos_id === null) { $this->setFlashMessage("ID tidak lengkap.", "error"); $this->redirect('admin/kos'); return; }
        $gambar_id_filtered = filter_var($gambar_id, FILTER_VALIDATE_INT);
        $kos_id_filtered = filter_var($kos_id, FILTER_VALIDATE_INT);
        if (!$gambar_id_filtered || !$kos_id_filtered ) { $this->setFlashMessage("Format ID tidak valid.", "error"); $this->redirect('admin/kosEdit/' . ($kos_id_filtered ?: $kos_id)); return;}

        $gambarData = $this->kosModel->deleteGambarKosById($gambar_id_filtered);
        if ($gambarData) {
            $filePathSystem = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $gambarData['path'];
            if (file_exists($filePathSystem)) {
                if (@unlink($filePathSystem)) { 
                    $this->setFlashMessage("Gambar '" . htmlspecialchars($gambarData['nama_file']) . "' berhasil dihapus.", "success");
                    if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin menghapus gambar ID: {$gambar_id_filtered} dari kos ID: {$kos_id_filtered}", $_SESSION['user_id']);
                } else { $this->setFlashMessage("Gagal menghapus file gambar dari server.", "warning");}
            } else { $this->setFlashMessage("File gambar tidak ditemukan di server, data DB dihapus.", "info"); }
        } else { $this->setFlashMessage("Gagal menghapus data gambar dari DB.", "error");}
        $this->redirect('admin/kosEdit/' . $kos_id_filtered);
    }

    public function kosDelete($id = null): void { 
        if ($id === null) { $this->setFlashMessage("ID Kos tidak ada.", "error"); $this->redirect('admin/kos'); return; }
        $kos_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) { $this->setFlashMessage("ID Kos tidak valid.", "error"); $this->redirect('admin/kos'); return; }
        $kos = $this->kosModel->getKosById($kos_id_filtered);
        if (!$kos) { $this->setFlashMessage("Kos dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->kosModel->deleteKos($kos_id_filtered)) {
                if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin menghapus kos ID: {$kos_id_filtered} - Nama: " . htmlspecialchars($kos['nama_kos']), $_SESSION['user_id']);
                $this->setFlashMessage("Data kos '" . htmlspecialchars($kos['nama_kos']) . "' berhasil dihapus.", "success");
            } else { $this->setFlashMessage("Gagal menghapus data kos.", "error");}
            $this->redirect('admin/kos'); return;
        }
        $this->loadAdminView('kos/delete_confirm', 
            ['kos' => $kos, 'pageTitle' => 'Konfirmasi Hapus Kos', 'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']], 
            'Konfirmasi Hapus Kos'
        );
    }

    // --- USER MANAGEMENT ---
    public function users(): void {
        $pageTitle = "Manajemen Pengguna";
        $daftarPengguna = $this->userModel->getAllUsers();
       $this->loadAdminView('users/list', ['daftarPengguna' => $daftarPengguna], $pageTitle);
    }
    public function userEdit($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Pengguna tidak ada.", "error"); $this->redirect('admin/users'); return; }
        $user_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($user_id_filtered === false || $user_id_filtered <= 0) { $this->setFlashMessage("ID Pengguna tidak valid.", "error"); $this->redirect('admin/users'); return;}
        $user = $this->userModel->getUserById($user_id_filtered);
        if (!$user) { $this->setFlashMessage("Pengguna dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/users'); return; }

        $pageTitle = "Edit Pengguna: " . htmlspecialchars($user['nama']);
        $viewName = 'admin/users/form';
        $dataForView = ['formAction' => $this->appConfig['BASE_URL'] . 'admin/userEdit/' . $user['id'], 'user' => $user, 'pageTitle' => $pageTitle];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim(strip_tags($_POST['nama'] ?? ''));
            $no_telepon = !empty(trim(strip_tags($_POST['no_telepon'] ?? ''))) ? trim(strip_tags($_POST['no_telepon'])) : null;
            $alamat = !empty(trim(strip_tags($_POST['alamat'] ?? ''))) ? trim(strip_tags($_POST['alamat'])) : null;
            $is_admin_input = isset($_POST['is_admin']) && $_POST['is_admin'] == '1';
            $errors = [];
            if (empty($nama)) $errors[] = "Nama wajib diisi.";
            
            // Logika pencegahan admin terakhir menghapus status adminnya sendiri
            if ($user['id'] === $_SESSION['user_id'] && $user['is_admin'] && !$is_admin_input) {
                // Anda perlu method di UserModel untuk menghitung jumlah admin. Misal: $this->userModel->countActiveAdmins()
                // Untuk sekarang, kita asumsikan ada lebih dari 1 admin atau logika ini disederhanakan.
                // Jika hanya ada 1 admin, $errors[] = "Tidak bisa menghapus status admin diri sendiri jika hanya ada satu admin.";
            }

            $dataForView['user'] = array_merge($user, $_POST, ['is_admin' => $is_admin_input]); 
            if (empty($errors)) {
                if ($this->userModel->updateUserByAdmin($user['id'], $nama, $no_telepon, $alamat, $is_admin_input)) {
                    if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin mengedit User ID: {$user['id']}", $_SESSION['user_id']);
                    $this->setFlashMessage("Data pengguna berhasil diperbarui.", "success");
                    $this->redirect('admin/users'); return;
                } else { $this->setFlashMessage("Gagal memperbarui data pengguna atau tidak ada perubahan.", "error");}
            } else { $this->setFlashMessage(implode("<br>", $errors), "error");}
        }
        $this->loadAdminView('users/form', $dataForView, $pageTitle);
    }

    // --- BOOKING MANAGEMENT ---
    public function bookings(): void {
        $pageTitle = "Manajemen Pemesanan";
        $daftarBooking = $this->bookingModel->getAllBookings();
        $this->loadAdminView('bookings/list', ['daftarBooking' => $daftarBooking], $pageTitle);
    }
    public function bookingDetail($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }
        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        if (!$booking) { $this->setFlashMessage("Detail booking ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/bookings'); return; }
        $pageTitle = "Detail Pemesanan #" . htmlspecialchars($booking['booking_id_val']);
        $this->loadAdminView('bookings/detail', ['booking' => $booking], $pageTitle);
    }
    public function bookingConfirm($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }
        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        if (!$booking || $booking['status_pemesanan'] !== 'pending') { $this->setFlashMessage("Booking tidak bisa dikonfirmasi (bukan pending).", "warning"); $this->redirect('admin/bookings'); return; }
        $kos = $this->kosModel->getKosById($booking['kos_id']);
        if (!$kos || ($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kos['status_kos'] !== 'available') {
             $this->setFlashMessage("Konfirmasi Gagal: Kamar untuk '" . htmlspecialchars($kos['nama_kos'] ?? 'Kos') . "' habis/tidak tersedia.", "error");
             if ($booking) $this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected'); // Otomatis reject jika kamar habis
             if (isset($this->logAuditModel) && $booking) $this->logAuditModel->addLog("Admin otomatis mereject Booking ID: {$booking_id_filtered} karena kamar habis.", $_SESSION['user_id']);
             $this->redirect('admin/bookings'); return;
        }
        $this->pdo->beginTransaction();
        try {
            $kamarDecremented = $this->kosModel->decrementKamarTersedia($booking['kos_id']);
            if (!$kamarDecremented) throw new Exception("Gagal mengurangi jumlah kamar (kemungkinan sudah dipesan orang lain saat proses konfirmasi).");
            $bookingUpdated = $this->bookingModel->updateBookingStatus($booking_id_filtered, 'confirmed');
            if (!$bookingUpdated) throw new Exception("Gagal mengupdate status booking menjadi 'confirmed'.");
            $this->pdo->commit();
            if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin mengonfirmasi Booking ID: {$booking_id_filtered}", $_SESSION['user_id']);
            $this->setFlashMessage("Booking ID {$booking_id_filtered} berhasil dikonfirmasi.", "success");
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->setFlashMessage("Gagal konfirmasi booking: " . $e->getMessage(), "error");
        }
        $this->redirect('admin/bookings');
    }
    public function bookingReject($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Booking tidak ada.", "error"); $this->redirect('admin/bookings'); return; }
        $booking_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($booking_id_filtered === false || $booking_id_filtered <= 0) { $this->setFlashMessage("ID Booking tidak valid.", "error"); $this->redirect('admin/bookings'); return; }
        $booking = $this->bookingModel->getBookingById($booking_id_filtered);
        if (!$booking || $booking['status_pemesanan'] !== 'pending') { $this->setFlashMessage("Booking tidak bisa ditolak (bukan pending).", "warning"); $this->redirect('admin/bookings'); return; }
        if ($this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected')) {
            if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin menolak Booking ID: {$booking_id_filtered}", $_SESSION['user_id']);
            $this->setFlashMessage("Booking ID {$booking_id_filtered} berhasil ditolak.", "success");
        } else { $this->setFlashMessage("Gagal menolak booking ID {$booking_id_filtered}.", "error");}
        $this->redirect('admin/bookings');
    }
}
?>