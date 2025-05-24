<?php
// File: nama_proyek_kos/controllers/AdminController.php

class AdminController extends BaseController {
    private KosModel $kosModel;
    private UserModel $userModel;
    private BookingModel $bookingModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        // Pengecekan admin dilakukan sekali di constructor untuk semua method di controller ini
        $this->_checkAdmin(); 
        
        // Inisialisasi semua model yang dibutuhkan
        $this->kosModel = new KosModel($this->pdo);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
    }

    /**
     * Method privat untuk memeriksa apakah pengguna yang login adalah admin.
     * Jika bukan, akan diarahkan ke halaman lain.
     */
    private function _checkAdmin(): void {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            $this->setFlashMessage("Anda tidak memiliki hak akses ke halaman admin.", "error");
            if (isset($_SESSION['user_id'])) { // Jika login tapi bukan admin
                $this->redirect(''); // Arahkan ke halaman utama pengguna
            } else { // Jika belum login sama sekali
                $this->redirect('auth/login'); 
            }
            exit; // Hentikan eksekusi lebih lanjut
        }
    }

    /**
     * Menampilkan halaman utama dashboard admin.
     */
    public function dashboard(): void {
        $pageTitle = "Admin Dashboard";
        // Data untuk dashboard bisa ditambahkan nanti (misal statistik)
        $this->loadView('admin/dashboard', [], $pageTitle);
    }

    // --- START KOS CRUD ---
    /**
     * Menampilkan daftar semua kos untuk dikelola admin.
     */
    public function kos(): void { 
        $pageTitle = "Kelola Data Kos";
        $daftarKos = $this->kosModel->getAllKos('id', 'ASC');
        $this->loadView('admin/kos/list', ['daftarKos' => $daftarKos], $pageTitle);
    }

    /**
     * Helper method privat untuk menangani upload multiple gambar.
     * @param int $kosId ID Kos terkait.
     * @param array $filesData Data dari $_FILES['nama_input_file'].
     * @param string $uploadDirFs Path file sistem ke direktori upload (misal: 'C:/.../uploads/kos_images').
     * @return array Informasi gambar yang berhasil diupload.
     */
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
                        $this->setFlashMessage("Error Upload: Tipe file '{$originalName}' tidak diizinkan (hanya JPG, PNG, GIF, WEBP).", "error");
                        continue; 
                    }
                    if ($fileSize > $maxSize) {
                        $this->setFlashMessage("Error Upload: Ukuran file '{$originalName}' terlalu besar (maks 5MB).", "error");
                        continue;
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
                        $this->setFlashMessage("Error Sistem: Gagal memindahkan file '{$originalName}'. " . ($uploadError['message'] ?? ''), "error");
                    }
                } elseif ($filesData['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                    $this->setFlashMessage("Error Upload File '{$name}': Kode PHP " . $filesData['error'][$key], "error");
                }
            }
        }
        return $uploadedImageInfo;
    }

    /**
     * Menampilkan form tambah kos (GET) atau memproses data form (POST).
     */
    public function kosCreate(): void { 
        $pageTitle = "Tambah Kos Baru";
        $viewName = 'admin/kos/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosCreate',
            'kos' => null, 'mode' => 'create', 'pageTitle' => $pageTitle
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kosDataInput = [
                'nama_kos' => trim(strip_tags($_POST['nama_kos'] ?? '')),
                'alamat' => trim(strip_tags($_POST['alamat'] ?? '')),
                'deskripsi' => trim(strip_tags($_POST['deskripsi'] ?? '')),
                'harga_per_bulan' => filter_input(INPUT_POST, 'harga_per_bulan', FILTER_VALIDATE_FLOAT),
                'fasilitas_kos' => trim(strip_tags($_POST['fasilitas_kos'] ?? '')),
                'jumlah_kamar_total' => filter_input(INPUT_POST, 'jumlah_kamar_total', FILTER_VALIDATE_INT),
            ];
            
            $errors = [];
            if (empty($kosDataInput['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if (empty($kosDataInput['alamat'])) $errors[] = "Alamat wajib diisi.";
            if ($kosDataInput['harga_per_bulan'] === null || $kosDataInput['harga_per_bulan'] === false || $kosDataInput['harga_per_bulan'] <= 0) $errors[] = "Harga per bulan tidak valid.";
            if ($kosDataInput['jumlah_kamar_total'] === null || $kosDataInput['jumlah_kamar_total'] === false || $kosDataInput['jumlah_kamar_total'] < 0) {
                $errors[] = "Jumlah kamar total tidak valid (minimal 0).";
            }
            
            $dataForView['kos'] = $_POST; // Untuk prefill form jika ada error

            if (empty($errors)) {
                $newKosId = $this->kosModel->createKos($kosDataInput);
                if ($newKosId) {
                    $newKosId = (int)$newKosId;
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($newKosId, $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                    $this->setFlashMessage("Data kos baru berhasil ditambahkan.", "success");
                    $this->redirect('admin/kos'); 
                    return;
                } else { 
                    $this->setFlashMessage("Gagal menambahkan data kos ke database.", "error");
                }
            } else { 
                $this->setFlashMessage(implode("<br>", $errors), "error");
            }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }

    /**
     * Menampilkan form edit kos (GET) atau memproses update (POST).
     * @param int|string|null $id ID Kos.
     */
    public function kosEdit($id = null): void { 
        if ($id === null) { $this->setFlashMessage("ID Kos tidak ada.", "error"); $this->redirect('admin/kos'); return; }
        $kos_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) { $this->setFlashMessage("ID Kos tidak valid.", "error"); $this->redirect('admin/kos'); return; }

        $kos = $this->kosModel->getKosById($kos_id_filtered);
        if (!$kos) { $this->setFlashMessage("Kos dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }

        $pageTitle = "Edit Data Kos: " . htmlspecialchars($kos['nama_kos']);
        $viewName = 'admin/kos/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id'],
            'kos' => $kos, 'mode' => 'edit', 'pageTitle' => $pageTitle
        ];
        
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
            ];
            $errors = [];
            if (empty($updateData['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if ($updateData['harga_per_bulan'] === null || $updateData['harga_per_bulan'] === false || $updateData['harga_per_bulan'] <=0) $errors[] = "Harga tidak valid.";
            if ($updateData['jumlah_kamar_total'] === null || $updateData['jumlah_kamar_total'] < 0) $errors[] = "Jumlah kamar total tidak valid.";
            if ($updateData['jumlah_kamar_tersedia'] === null || $updateData['jumlah_kamar_tersedia'] < 0 || $updateData['jumlah_kamar_tersedia'] > $updateData['jumlah_kamar_total']) {
                 $errors[] = "Jumlah kamar tersedia tidak valid atau melebihi jumlah total.";
            }
            $allowed_statuses = ['available', 'booked', 'maintenance'];
            if (!in_array($updateData['status_kos'], $allowed_statuses)) $errors[] = "Status kos tidak valid.";
            
            $dataForView['kos'] = array_merge($kos, $updateData); 

            if (empty($errors)) {
                if ($this->kosModel->updateKos($kos['id'], $updateData)) {
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($kos['id'], $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                    $this->setFlashMessage("Data kos berhasil diperbarui.", "success");
                    $this->redirect('admin/kosEdit/' . $kos['id']); 
                    return;
                } else { 
                    if (empty($_SESSION['flash_message'])) { // Cek jika _handleImageUploads sudah set pesan
                        $this->setFlashMessage("Tidak ada perubahan data atau gagal memperbarui data kos.", "info");
                    }
                }
            } else { 
                $this->setFlashMessage(implode("<br>", $errors), "error");
            }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }
    
    /**
     * Menangani penghapusan gambar kos.
     */
    public function kosDeleteGambar($gambar_id = null, $kos_id = null): void {
        if ($gambar_id === null || $kos_id === null) { $this->setFlashMessage("ID tidak lengkap.", "error"); $this->redirect('admin/kos'); return; }
        $gambar_id_filtered = filter_var($gambar_id, FILTER_VALIDATE_INT);
        $kos_id_filtered = filter_var($kos_id, FILTER_VALIDATE_INT);
        if (!$gambar_id_filtered || !$kos_id_filtered ) { $this->setFlashMessage("Format ID tidak valid.", "error"); $this->redirect('admin/kosEdit/' . ($kos_id_filtered ?: $kos_id)); return;}

        $gambarData = $this->kosModel->deleteGambarKosById($gambar_id_filtered);
        if ($gambarData) {
            $filePathSystem = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $gambarData['path'];
            if (file_exists($filePathSystem)) {
                if (@unlink($filePathSystem)) { // @ untuk menekan warning jika gagal (sudah dihandle)
                    $this->setFlashMessage("Gambar '" . htmlspecialchars($gambarData['nama_file']) . "' berhasil dihapus.", "success");
                } else { $this->setFlashMessage("Gagal menghapus file gambar dari server. Periksa izin.", "warning");}
            } else { $this->setFlashMessage("File gambar tidak ditemukan di server, data DB dihapus.", "info"); }
        } else { $this->setFlashMessage("Gagal menghapus data gambar dari DB atau gambar tidak ada.", "error");}
        $this->redirect('admin/kosEdit/' . $kos_id_filtered);
    }

    /**
     * Menampilkan konfirmasi dan memproses penghapusan data kos.
     */
    public function kosDelete($id = null): void { 
        if ($id === null) { $this->setFlashMessage("ID Kos tidak ada.", "error"); $this->redirect('admin/kos'); return; }
        $kos_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) { $this->setFlashMessage("ID Kos tidak valid.", "error"); $this->redirect('admin/kos'); return; }

        $kos = $this->kosModel->getKosById($kos_id_filtered);
        if (!$kos) { $this->setFlashMessage("Kos dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/kos'); return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Disarankan ada validasi token CSRF di sini untuk keamanan
            if ($this->kosModel->deleteKos($kos_id_filtered)) {
                $this->setFlashMessage("Data kos '" . htmlspecialchars($kos['nama_kos']) . "' dan semua data terkait (gambar, booking) berhasil dihapus.", "success");
            } else { $this->setFlashMessage("Gagal menghapus data kos.", "error");}
            $this->redirect('admin/kos'); 
            return;
        }
        $this->loadView('admin/kos/delete', 
            ['kos' => $kos, 'pageTitle' => 'Konfirmasi Hapus Kos', 'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']], 
            'Konfirmasi Hapus Kos'
        );
    }
    // --- END KOS CRUD ---


    // --- START USER MANAGEMENT ---
    public function users(): void {
        $pageTitle = "Kelola Pengguna";
        $daftarPengguna = $this->userModel->getAllUsers();
        $this->loadView('admin/users/list', ['daftarPengguna' => $daftarPengguna], $pageTitle);
    }

    public function userEdit($id = null): void {
        if ($id === null) { $this->setFlashMessage("ID Pengguna tidak ada.", "error"); $this->redirect('admin/users'); return; }
        $user_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($user_id_filtered === false || $user_id_filtered <= 0) { $this->setFlashMessage("ID Pengguna tidak valid.", "error"); $this->redirect('admin/users'); return;}
        
        $user = $this->userModel->getUserById($user_id_filtered);
        if (!$user) { $this->setFlashMessage("Pengguna dengan ID {$id} tidak ditemukan.", "error"); $this->redirect('admin/users'); return; }

        $pageTitle = "Edit Pengguna: " . htmlspecialchars($user['nama']);
        $viewName = 'admin/users/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/userEdit/' . $user['id'],
            'user' => $user, 'pageTitle' => $pageTitle
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim(strip_tags($_POST['nama'] ?? ''));
            $no_telepon_input = trim(strip_tags($_POST['no_telepon'] ?? ''));
            $no_telepon = !empty($no_telepon_input) ? $no_telepon_input : null;
            $alamat_input = trim(strip_tags($_POST['alamat'] ?? ''));
            $alamat = !empty($alamat_input) ? $alamat_input : null;
            $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] == '1'; // Checkbox value '1' jika tercentang
            
            $errors = [];
            if (empty($nama)) $errors[] = "Nama wajib diisi.";
            // Validasi lain jika perlu

            $dataForView['user'] = array_merge($user, $_POST, ['is_admin' => $is_admin]); // Update data untuk form jika error

            if (empty($errors)) {
                if ($this->userModel->updateUserByAdmin($user['id'], $nama, $no_telepon, $alamat, $is_admin)) {
                    $this->setFlashMessage("Data pengguna berhasil diperbarui.", "success");
                    $this->redirect('admin/users'); 
                    return;
                } else { $this->setFlashMessage("Gagal memperbarui data pengguna atau tidak ada perubahan.", "error");}
            } else { $this->setFlashMessage(implode("<br>", $errors), "error");}
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }
    // --- END USER MANAGEMENT ---


    // --- START BOOKING MANAGEMENT ---
    public function bookings(): void {
        $pageTitle = "Kelola Pemesanan";
        $daftarBooking = $this->bookingModel->getAllBookings();
        $this->loadView('admin/bookings/list', ['daftarBooking' => $daftarBooking], $pageTitle);
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
            $this->setFlashMessage("Booking tidak ditemukan atau statusnya bukan 'pending'.", "warning");
            $this->redirect('admin/bookings');
            return;
        }

        $kos = $this->kosModel->getKosById($booking['kos_id']);
        if (!$kos || ($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kos['status_kos'] !== 'available') {
             $this->setFlashMessage("Konfirmasi Gagal: Kamar untuk '" . htmlspecialchars($kos['nama_kos'] ?? 'Kos') . "' habis/tidak tersedia.", "error");
             if ($booking) $this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected');
             $this->redirect('admin/bookings');
             return;
        }
        
        $this->pdo->beginTransaction();
        try {
            $kamarDecremented = $this->kosModel->decrementKamarTersedia($booking['kos_id']);
            if (!$kamarDecremented) {
                throw new Exception("Gagal mengurangi jumlah kamar. Mungkin kamar terakhir baru saja dipesan.");
            }
            $bookingUpdated = $this->bookingModel->updateBookingStatus($booking_id_filtered, 'confirmed');
            if (!$bookingUpdated) {
                throw new Exception("Gagal mengupdate status booking menjadi 'confirmed'.");
            }
            $this->pdo->commit();
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
        if (!$booking || $booking['status_pemesanan'] !== 'pending') {
            $this->setFlashMessage("Booking tidak ditemukan atau statusnya bukan 'pending'.", "warning");
            $this->redirect('admin/bookings');
            return;
        }
        
        if ($this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected')) {
            $this->setFlashMessage("Booking ID {$booking_id_filtered} berhasil ditolak.", "success");
        } else {
            $this->setFlashMessage("Gagal menolak booking ID {$booking_id_filtered}.", "error");
        }
        $this->redirect('admin/bookings');
    }
    // --- END BOOKING MANAGEMENT ---
}
?>