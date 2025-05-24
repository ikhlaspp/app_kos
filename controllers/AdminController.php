<?php
// File: nama_proyek_kos/controllers/AdminController.php

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

    private function _handleImageUploads(int $kosId, array $filesData, string $uploadDirFs): array {
        $uploadedImageInfo = []; // Akan menyimpan nama asli dan path relatif
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Pastikan $uploadDirFs ada dan bisa ditulis
        if (!is_dir($uploadDirFs)) {
            if (!mkdir($uploadDirFs, 0775, true)) {
                $this->setFlashMessage("Error: Gagal membuat direktori upload ({$uploadDirFs}). Periksa izin.", "error");
                return $uploadedImageInfo; // Kembalikan array kosong jika direktori tidak bisa dibuat
            }
        }
        if (!is_writable($uploadDirFs)) {
             $this->setFlashMessage("Error: Direktori upload ({$uploadDirFs}) tidak dapat ditulis. Periksa izin.", "error");
             return $uploadedImageInfo;
        }


        // Cek apakah ada file yang diupload (struktur $_FILES untuk multiple sedikit berbeda)
        if (isset($filesData['name']) && is_array($filesData['name'])) {
            foreach ($filesData['name'] as $key => $name) {
                // Lewati jika tidak ada file yang diupload untuk entri array ini (misalnya, input file kosong)
                if ($filesData['error'][$key] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                
                if ($filesData['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $filesData['tmp_name'][$key];
                    $originalName = basename($name); // Nama file asli dari klien
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
                    // Membuat nama file yang lebih aman dan unik
                    $safeOriginalName = preg_replace("/[^a-zA-Z0-9._-]/", "", pathinfo($originalName, PATHINFO_FILENAME));
                    $uniqueFileName = uniqid($safeOriginalName . '_', true) . '.' . $extension;
                    
                    $destinationPathFs = rtrim($uploadDirFs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $uniqueFileName;
                    // Path yang disimpan di DB adalah relatif terhadap folder utama 'uploads/' yang bisa diakses publik
                    $relativePathForDb = 'kos_images/' . $uniqueFileName; 

                    if (move_uploaded_file($tmpName, $destinationPathFs)) {
                        if ($this->kosModel->addGambarKos($kosId, $originalName, $relativePathForDb)) {
                            $uploadedImageInfo[] = ['name' => $originalName, 'path' => $relativePathForDb];
                        } else {
                            $this->setFlashMessage("Error DB: Gagal menyimpan info gambar '{$originalName}' ke database.", "error");
                            if (file_exists($destinationPathFs)) unlink($destinationPathFs); 
                        }
                    } else {
                        // Dapatkan detail error dari move_uploaded_file jika ada (PHP 8+)
                        $uploadError = error_get_last();
                        $this->setFlashMessage("Error Sistem: Gagal memindahkan file upload '{$originalName}'. " . ($uploadError['message'] ?? 'Periksa izin folder.'), "error");
                    }
                } elseif ($filesData['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                    $this->setFlashMessage("Error Upload File '{$name}': Kode error PHP " . $filesData['error'][$key], "error");
                }
            }
        }
        return $uploadedImageInfo;
    }

    public function kosCreate(): void { 
        $pageTitle = "Tambah Kos Baru";
        $viewName = 'admin/kos/form';
        $dataForView = [
            'formAction' => $this->appConfig['BASE_URL'] . 'admin/kosCreate',
            'kos' => null, 'mode' => 'create', 'pageTitle' => $pageTitle
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_kos = trim(strip_tags($_POST['nama_kos'] ?? ''));
            $alamat = trim(strip_tags($_POST['alamat'] ?? ''));
            $deskripsi = trim(strip_tags($_POST['deskripsi'] ?? ''));
            $harga_per_bulan = filter_input(INPUT_POST, 'harga_per_bulan', FILTER_VALIDATE_FLOAT);
            $fasilitas_kos = trim(strip_tags($_POST['fasilitas_kos'] ?? ''));
            $jumlah_kamar_total = filter_input(INPUT_POST, 'jumlah_kamar_total', FILTER_VALIDATE_INT);
            
            $errors = [];
            if (empty($nama_kos)) $errors[] = "Nama kos wajib diisi.";
            if (empty($alamat)) $errors[] = "Alamat wajib diisi.";
            if ($harga_per_bulan === null || $harga_per_bulan === false || $harga_per_bulan <= 0) $errors[] = "Harga per bulan tidak valid.";
            if ($jumlah_kamar_total === null || $jumlah_kamar_total === false || $jumlah_kamar_total < 0) { // Kamar total bisa 0 jika memang tidak ada unit
                $errors[] = "Jumlah kamar total tidak valid (minimal 0).";
            }
            
            $dataForView['kos'] = $_POST; // Kirim kembali input jika ada error untuk prefill

            if (empty($errors)) {
                $kosData = [
                    'nama_kos' => $nama_kos,
                    'alamat' => $alamat,
                    'deskripsi' => $deskripsi,
                    'harga_per_bulan' => $harga_per_bulan,
                    'fasilitas_kos' => $fasilitas_kos,
                    'jumlah_kamar_total' => $jumlah_kamar_total,
                    // status_kos dan jumlah_kamar_tersedia akan dihandle oleh KosModel::createKos
                ];
                $newKosId = $this->kosModel->createKos($kosData); // Ini mengembalikan ID kos baru atau false
                
                if ($newKosId) {
                    $newKosId = (int)$newKosId; // Pastikan integer
                    // Handle upload gambar setelah kos berhasil dibuat
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        // Pastikan UPLOADS_FS_PATH di appConfig sudah benar
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($newKosId, $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                    $this->setFlashMessage("Data kos baru berhasil ditambahkan.", "success");
                    $this->redirect('admin/kos'); 
                    return;
                } else { 
                    $this->setFlashMessage("Gagal menambahkan data kos. Terjadi kesalahan saat menyimpan ke database.", "error");
                }
            } else { 
                $this->setFlashMessage(implode("<br>", $errors), "error");
            }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }

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
            'kos' => $kos, 
            'mode' => 'edit', 
            'pageTitle' => $pageTitle
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

            // Siapkan data untuk view jika ada error, gabungkan data asli dengan data POST
            $dataForView['kos'] = array_merge($kos, $updateData); 

            if (empty($errors)) {
                if ($this->kosModel->updateKos($kos['id'], $updateData)) {
                    // Handle upload gambar baru jika ada
                    if (isset($_FILES['gambar_kos_baru']) && !empty($_FILES['gambar_kos_baru']['name'][0])) {
                        $uploadDirectory = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kos_images';
                        $this->_handleImageUploads($kos['id'], $_FILES['gambar_kos_baru'], $uploadDirectory);
                    }
                    $this->setFlashMessage("Data kos berhasil diperbarui.", "success");
                    $this->redirect('admin/kosEdit/' . $kos['id']); // Kembali ke form edit untuk lihat perubahan
                    return;
                } else { 
                    // Jika updateKos return false, bisa jadi tidak ada perubahan atau error DB
                    // Pesan flash sudah dihandle oleh _handleImageUploads jika ada masalah gambar
                    // Tambahkan pengecekan apakah ada pesan flash dari _handleImageUploads sebelum set pesan ini
                    if (empty($_SESSION['flash_message'])) {
                        $this->setFlashMessage("Tidak ada perubahan data atau gagal memperbarui data kos.", "info");
                    }
                }
            } else { 
                $this->setFlashMessage(implode("<br>", $errors), "error");
            }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }
    
    public function kosDeleteGambar($gambar_id = null, $kos_id = null): void {
        // ... (kode kosDeleteGambar dari jawaban sebelumnya, sudah benar) ...
        if ($gambar_id === null || $kos_id === null) { /* ... */ $this->redirect('admin/kos'); return; }
        $gambar_id_filtered = filter_var($gambar_id, FILTER_VALIDATE_INT);
        $kos_id_filtered = filter_var($kos_id, FILTER_VALIDATE_INT);
        if (!$gambar_id_filtered || !$kos_id_filtered ) { /* ... */ $this->redirect('admin/kosEdit/' . $kos_id_filtered); return;}

        $gambarData = $this->kosModel->deleteGambarKosById($gambar_id_filtered);
        if ($gambarData) {
            $filePathSystem = rtrim($this->appConfig['UPLOADS_FS_PATH'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $gambarData['path'];
            if (file_exists($filePathSystem)) {
                if (unlink($filePathSystem)) {
                    $this->setFlashMessage("Gambar '" . htmlspecialchars($gambarData['nama_file']) . "' berhasil dihapus.", "success");
                } else {
                    $this->setFlashMessage("Gagal menghapus file gambar dari server.", "warning");
                }
            } else { $this->setFlashMessage("File gambar tidak ditemukan di server, data DB dihapus.", "info"); }
        } else { $this->setFlashMessage("Gagal menghapus data gambar dari DB.", "error");}
        $this->redirect('admin/kosEdit/' . $kos_id_filtered);
    }

    // ... (method kosDelete, users, userEdit, bookings, bookingDetail, bookingConfirm, bookingReject sudah ada) ...
}
?>