<?php
// File: controllers/AdminController.php

class AdminController extends BaseController {
    private KosModel $kosModel;
    private UserModel $userModel;
    private BookingModel $bookingModel;
    private LogAuditModel $logAuditModel;
    private VoucherModel $voucherModel; // ADDED: VoucherModel declaration

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->_checkAdmin(); 
        
        $this->kosModel = new KosModel($this->pdo);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
        $this->voucherModel = new VoucherModel($this->pdo); // ADDED: VoucherModel initialization

        if (class_exists('LogAuditModel')) {
            $this->logAuditModel = new LogAuditModel($this->pdo);
        } else {
            error_log("Peringatan: Class LogAuditModel tidak ditemukan. Fitur log audit tidak akan aktif.");
        }
    }

    protected function loadAdminView(string $viewName, array $data = [], ?string $pageTitle = null): void {
        $data['appConfig'] = $this->appConfig;
        $data['pageTitle'] = $pageTitle ?? ($data['pageTitle'] ?? 'Admin Panel');
        $data['contentView'] = 'admin/' . $viewName;

        $layoutAdminPath = $this->appConfig['VIEWS_PATH'] . 'admin/layout_admin.php';

        if (file_exists($layoutAdminPath)) {
            extract($data);
            require_once $layoutAdminPath;
        } else {
            http_response_code(500);
            error_log("Error: File layout admin tidak ditemukan: " . $layoutAdminPath);
            echo "Error: File layout admin tidak ditemukan.";
        }
    }

    private function _checkAdmin(): void {
        if (!$this->isLoggedIn() || !$this->isAdmin()) {
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
        $totalBookings = $this->bookingModel->countTotalBookings() ?? 0;
        $recentConfirmedBookings = $this->bookingModel->getRecentConfirmedBookings(5) ?? [];
        
        $recentLogs = [];
        if (isset($this->logAuditModel)) {
            $recentLogs = $this->logAuditModel->getRecentLogs(7) ?? [];
        }

        $data = [
            'totalUsers' => $totalUsers,
            'totalKos' => $totalKos,
            'pendingBookings' => $pendingBookings,
            'totalBookings' => $totalBookings,
            'recentConfirmedBookings' => $recentConfirmedBookings,
            'recentLogs' => $recentLogs,
        ];
        $this->loadAdminView('dashboard', $data, $pageTitle); 
    }

    // --- NEW: API endpoint for chart data ---
    public function getBookingChartData(): void {
        header('Content-Type: application/json');

        $numMonths = $this->getInputGet('months', 12, FILTER_VALIDATE_INT);
        if ($numMonths < 1) $numMonths = 12;

        $monthlySummary = $this->bookingModel->getMonthlyBookingSummary($numMonths);

        $labels = array_keys($monthlySummary);
        $data = array_values($monthlySummary);

        echo json_encode([
            'labels' => $labels,
            'data' => $data
        ]);
        exit;
    }
    // --- END NEW ---

    // --- KOS CRUD ---
    public function kos(): void { 
        $pageTitle = "Manajemen Data Kos";

        $filterValues = [
            'search_term' => $this->getInputGet('search_term', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'kategori'    => $this->getInputGet('kategori', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'min_harga'   => $this->getInputGet('min_harga', null, FILTER_VALIDATE_FLOAT),
            'max_harga'   => $this->getInputGet('max_harga', null, FILTER_VALIDATE_FLOAT),
            'status'      => $this->getInputGet('status', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'fasilitas'   => $this->getInputGet('fasilitas', null, FILTER_SANITIZE_SPECIAL_CHARS),
        ];
        foreach ($filterValues as $key => $value) {
            if ($value === '') {
                $filterValues[$key] = null;
            }
        }

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if ($currentPage < 1) $currentPage = 1;
        
        $itemsPerPage = 10;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $daftarKos = $this->kosModel->getAllKos($filterValues, 'id', 'ASC', $itemsPerPage, $offset);
        
        $totalFilteredKos = $this->kosModel->countAllKosFiltered($filterValues);
        $totalPages = ($itemsPerPage > 0) ? ceil($totalFilteredKos / $itemsPerPage) : 1;
        if ($totalPages < 1) $totalPages = 1;
        if ($currentPage > $totalPages && $totalPages > 0) { $currentPage = $totalPages; }

        $filterParamsForUrl = array_filter($filterValues, function($value) { return $value !== null;});
        $filterQueryString = !empty($filterParamsForUrl) ? '&' . http_build_query($filterParamsForUrl) : '';
        
        $paginationBaseUrl = $this->appConfig['BASE_URL'] . 'admin/kos';

        $data = [
            'daftarKos'    => $daftarKos,
            'filterValues' => $filterValues,
            'pagination'   => [
                'currentPage'   => $currentPage,
                'totalPages'    => $totalPages,
                'baseUrl'       => $paginationBaseUrl,
                'queryString'   => $filterQueryString
            ]
        ];
        
        $this->loadAdminView('kos/list', $data, $pageTitle);
    }
    
    private function _handleImageUploads(int $kosId, array $filesData, string $uploadDirFs): array {
        $uploadedImageInfo = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;

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
                'kategori' => $this->getInputPost('kategori', null, FILTER_SANITIZE_SPECIAL_CHARS),
                'status_kos' => $this->getInputPost('status_kos', null, FILTER_SANITIZE_SPECIAL_CHARS)
            ];
            $errors = [];
            if (empty($kosDataInput['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if (empty($kosDataInput['alamat'])) $errors[] = "Alamat wajib diisi.";
            if ($kosDataInput['harga_per_bulan'] === null || $kosDataInput['harga_per_bulan'] === false || $kosDataInput['harga_per_bulan'] <= 0) { $errors[] = "Harga per bulan tidak valid."; }
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
                    return;
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
                'kategori' => $this->getInputPost('kategori', null, FILTER_SANITIZE_SPECIAL_CHARS)
            ];
            $errors = [];
            if (empty($updateData['nama_kos'])) $errors[] = "Nama kos wajib diisi.";
            if ($updateData['harga_per_bulan'] === null || $updateData['harga_per_bulan'] === false || $updateData['harga_per_bulan'] <=0) { $errors[] = "Harga tidak valid."; }
            
            $dataForView['kos'] = array_merge($kos, $_POST, ['jumlah_kamar_tersedia' => $updateData['jumlah_kamar_tersedia']]); // Merge current post data to retain changes on error
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
            } else { $this->setFlashMessage(implode("<br>", $errors), "error");}
        }
        $this->loadAdminView('kos/form', $dataForView, $pageTitle);
    }
    
    public function kosDeleteGambar($gambar_id = null, $kos_id = null): void {
        if ($gambar_id === null || $kos_id === null) { $this->setFlashMessage("ID tidak lengkap.", "error"); $this->redirect('admin/kos'); return; }
        $gambar_id_filtered = filter_var($gambar_id, FILTER_VALIDATE_INT);
        $kos_id_filtered = filter_var($kos_id, FILTER_VALIDATE_INT);
        if (!$gambar_id_filtered || !$kos_id_filtered ) { $this->setFlashMessage("Format ID tidak valid.", "error"); $this->redirect('admin/kosEdit/' . ($kos_id_filtered ?: $kos_id)); return;}

        // Fetch image path BEFORE attempting to delete from DB
        $gambarPath = $this->kosModel->getGambarPathById($gambar_id_filtered); // Assuming a method to get path only
        
        if ($this->kosModel->deleteGambarKosById($gambar_id_filtered)) { // This method should return true/false for success
            if ($gambarPath) { // Only attempt file deletion if path was found
                $filePathSystem = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $gambarPath;
                if (file_exists($filePathSystem)) {
                    if (@unlink($filePathSystem)) { 
                        $this->setFlashMessage("Gambar berhasil dihapus dari server.", "success");
                        if (isset($this->logAuditModel)) $this->logAuditModel->addLog("Admin menghapus gambar ID: {$gambar_id_filtered} dari kos ID: {$kos_id_filtered}", $_SESSION['user_id']);
                    } else { $this->setFlashMessage("Gagal menghapus file gambar dari server.", "warning");}
                } else { $this->setFlashMessage("File gambar tidak ditemukan di server, data DB dihapus.", "info"); }
            } else { // Image data deleted from DB, but no path or path not found
                 $this->setFlashMessage("Gambar berhasil dihapus dari database, namun file tidak ditemukan.", "info");
            }
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
            
            // This logic seems incomplete or intended for a specific scenario where an admin cannot demote themselves
            // if ($user['id'] === $_SESSION['user_id'] && $user['is_admin'] && !$is_admin_input) {
            //     $errors[] = "Anda tidak dapat mencabut status admin Anda sendiri.";
            // }

            $dataForView['user'] = array_merge($user, $_POST, ['is_admin' => $is_admin_input]); 
            if (empty($errors)) {
                if ($this->userModel->updateUserByAdmin($user['id'], $nama, $no_telepon, $alamat, $is_admin_input)) {
                    // Update current user's session if their own admin status changes
                    if ($user['id'] === $_SESSION['user_id']) {
                        $_SESSION['is_admin'] = $is_admin_input;
                    }

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
            if ($booking) $this->bookingModel->updateBookingStatus($booking_id_filtered, 'rejected');
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

    // --- VOUCHER MANAGEMENT (CRUD) ---
    public function voucher(): void {
        $pageTitle = "Manajemen Data Voucher";
        $vouchers = $this->voucherModel->getAllVouchers(); 
        $data = [
            'vouchers' => $vouchers,
        ];

         $paginationBaseUrl = $this->appConfig['BASE_URL'] . 'admin/voucher';

        $this->loadAdminView('voucher/list', $data, $pageTitle);
    }

    public function createVoucher(): void {
        $pageTitle = "Buat Voucher Baru";
        $data = [
            'errors' => [],   // To store validation errors
            'oldInput' => []  // To repopulate form fields on validation failure
        ];
        $this->loadAdminView('voucher/form', $data, $pageTitle);
    }

    public function storeVoucher(): void {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = [];
            $input = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Basic Validation
            if (empty($input['code'])) {
                $errors['code'] = 'Kode voucher harus diisi.';
            }
            if (empty($input['name'])) {
                $errors['name'] = 'Nama voucher harus diisi.';
            }
            if (empty($input['type']) || !in_array($input['type'], ['percentage', 'fixed_amount'])) {
                $errors['type'] = 'Tipe voucher tidak valid (harus "percentage" atau "fixed_amount").';
            }
            if (!is_numeric($input['value']) || $input['value'] <= 0) {
                $errors['value'] = 'Nilai voucher harus angka positif.';
            }
            if (empty($input['expiration_date'])) {
                $errors['expiration_date'] = 'Tanggal kadaluarsa harus diisi.';
            } elseif (!strtotime($input['expiration_date'])) {
                $errors['expiration_date'] = 'Format tanggal kadaluarsa tidak valid.';
            }

            // Prepare optional fields and checkboxes
            $input['min_transaction_amount'] = !empty($input['min_transaction_amount']) && is_numeric($input['min_transaction_amount']) ? $input['min_transaction_amount'] : null;
            $input['max_discount_amount'] = !empty($input['max_discount_amount']) && is_numeric($input['max_discount_amount']) ? $input['max_discount_amount'] : null;
            $input['usage_limit_per_user'] = !empty($input['usage_limit_per_user']) && is_numeric($input['usage_limit_per_user']) ? (int)$input['usage_limit_per_user'] : 1;
            $input['total_usage_limit'] = !empty($input['total_usage_limit']) && is_numeric($input['total_usage_limit']) ? (int)$input['total_usage_limit'] : null;
            $input['is_active'] = isset($input['is_active']) ? 1 : 0;
            $input['is_claimable_by_new_users'] = isset($input['is_claimable_by_new_users']) ? 1 : 0;
            $input['current_total_uses'] = 0; // New voucher starts with 0 uses

            if (empty($errors)) {
                if ($this->voucherModel->createVoucher($input)) {
                    if (isset($this->logAuditModel)) {
                        $this->logAuditModel->addLog("Admin membuat voucher baru: " . htmlspecialchars($input['code']), $_SESSION['user_id'], json_encode(['voucher_code' => $input['code']]));
                    }
                    $this->setFlashMessage("Voucher berhasil ditambahkan.", "success");
                    $this->redirect('admin/voucher');
                } else {
                    $errors['database'] = 'Terjadi kesalahan saat menyimpan voucher ke database. Silakan coba lagi.';
                    error_log("Database error creating voucher: " . print_r($input, true)); // Log for debugging
                }
            }

            // If there are errors, reload the form with existing input and errors
            $data = [
                'errors' => $errors,
                'oldInput' => $input
            ];
            $this->loadAdminView('voucher/create', $data, $pageTitle);

        } else {
            // If request is not POST, redirect to the form
            $this->redirect('admin/createVoucher');
        }
    }

    public function editVoucher($id = null): void {
        if ($id === null) {
            $this->setFlashMessage("ID Voucher tidak ada.", "error");
            $this->redirect('admin/voucher');
            return;
        }
        $voucher_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($voucher_id_filtered === false || $voucher_id_filtered <= 0) {
            $this->setFlashMessage("ID Voucher tidak valid.", "error");
            $this->redirect('admin/voucher');
            return;
        }

        $voucher = $this->voucherModel->getVoucherById($voucher_id_filtered);

        if (!$voucher) {
            $this->setFlashMessage("Voucher tidak ditemukan.", "error");
            $this->redirect('admin/voucher');
            return;
        }

        $pageTitle = 'Edit Voucher: ' . htmlspecialchars($voucher['code']);
        $data = [
            'voucher' => $voucher, // Original voucher data
            'errors' => [],
            'oldInput' => $voucher // Pre-populate form with current voucher data
        ];
        $this->loadAdminView('voucher/form', $data, $pageTitle);
    }

    public function updateVoucher($id = null): void {
        if ($id === null) {
            $this->setFlashMessage("ID Voucher tidak ada.", "error");
            $this->redirect('admin/voucher');
            return;
        }
        $voucher_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($voucher_id_filtered === false || $voucher_id_filtered <= 0) {
            $this->setFlashMessage("ID Voucher tidak valid.", "error");
            $this->redirect('admin/voucher');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = [];
            $input = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $input['id'] = $voucher_id_filtered; // Ensure the ID is part of the data to be updated

            // Basic Validation
            if (empty($input['code'])) {
                $errors['code'] = 'Kode voucher harus diisi.';
            }
            if (empty($input['name'])) {
                $errors['name'] = 'Nama voucher harus diisi.';
            }
            if (empty($input['type']) || !in_array($input['type'], ['percentage', 'fixed_amount'])) {
                $errors['type'] = 'Tipe voucher tidak valid (harus "percentage" atau "fixed_amount").';
            }
            if (!is_numeric($input['value']) || $input['value'] <= 0) {
                $errors['value'] = 'Nilai voucher harus angka positif.';
            }
            if (empty($input['expiration_date'])) {
                $errors['expiration_date'] = 'Tanggal kadaluarsa harus diisi.';
            } elseif (!strtotime($input['expiration_date'])) {
                $errors['expiration_date'] = 'Format tanggal kadaluarsa tidak valid.';
            }

            // Prepare optional fields and checkboxes
            $input['min_transaction_amount'] = !empty($input['min_transaction_amount']) && is_numeric($input['min_transaction_amount']) ? $input['min_transaction_amount'] : null;
            $input['max_discount_amount'] = !empty($input['max_discount_amount']) && is_numeric($input['max_discount_amount']) ? $input['max_discount_amount'] : null;
            $input['usage_limit_per_user'] = !empty($input['usage_limit_per_user']) && is_numeric($input['usage_limit_per_user']) ? (int)$input['usage_limit_per_user'] : 1;
            $input['total_usage_limit'] = !empty($input['total_usage_limit']) && is_numeric($input['total_usage_limit']) ? (int)$input['total_usage_limit'] : null;
            $input['is_active'] = isset($input['is_active']) ? 1 : 0;
            $input['is_claimable_by_new_users'] = isset($input['is_claimable_by_new_users']) ? 1 : 0;

            if (empty($errors)) {
                if ($this->voucherModel->updateVoucher($input)) {
                    if (isset($this->logAuditModel)) {
                        $this->logAuditModel->addLog("Admin memperbarui voucher ID: {$input['id']} (Code: " . htmlspecialchars($input['code']) . ")", $_SESSION['user_id'], json_encode(['voucher_id' => $input['id']]));
                    }
                    $this->setFlashMessage("Voucher berhasil diperbarui.", "success");
                    $this->redirect('admin/voucher');
                } else {
                    $errors['database'] = 'Terjadi kesalahan saat memperbarui voucher atau tidak ada perubahan yang terdeteksi. Silakan coba lagi.';
                    error_log("Database error updating voucher ID {$id}: " . print_r($input, true)); // Log for debugging
                }
            }

            // If there are errors, reload the form with the user's submitted input and errors
            $voucher = $this->voucherModel->getVoucherById($id); // Re-fetch original to ensure we have current state if needed
            $data = [
                'voucher' => $voucher,
                'errors' => $errors,
                'oldInput' => $input // Repopulate form with user's current submission
            ];
            $pageTitle = 'Edit Voucher: ' . htmlspecialchars($input['code'] ?? 'ID ' . $id);
            $this->loadAdminView('voucher/edit', $data, $pageTitle);

        } else {
            // If request is not POST, redirect to the edit form
            $this->redirect('admin/editVoucher/' . $id);
        }
    }

    public function deleteVoucher($id = null): void {
        if ($id === null) {
            $this->setFlashMessage("ID Voucher tidak ada.", "error");
            $this->redirect('admin/voucher');
            return;
        }
        $voucher_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($voucher_id_filtered === false || $voucher_id_filtered <= 0) {
            $this->setFlashMessage("ID Voucher tidak valid.", "error");
            $this->redirect('admin/voucher');
            return;
        }

        // Get voucher details before deleting for logging/flash message
        $voucher = $this->voucherModel->getVoucherById($voucher_id_filtered);
        $voucherCode = $voucher['code'] ?? 'N/A';

        // It's highly recommended to use POST for delete operations for security
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->voucherModel->deleteVoucher($voucher_id_filtered)) {
                if (isset($this->logAuditModel)) {
                    $this->logAuditModel->addLog("Admin menghapus voucher ID: {$voucher_id_filtered} (Code: " . htmlspecialchars($voucherCode) . ")", $_SESSION['user_id'], json_encode(['voucher_id' => $voucher_id_filtered]));
                }
                $this->setFlashMessage("Voucher '{$voucherCode}' berhasil dihapus.", "success");
            } else {
                $this->setFlashMessage("Gagal menghapus voucher '{$voucherCode}'. Mungkin voucher tidak ditemukan atau terjadi kesalahan database.", "error");
            }
        } else {
            $this->setFlashMessage("Metode tidak diizinkan untuk operasi penghapusan.", "error");
        }
        $this->redirect('admin/voucher');
    }
}