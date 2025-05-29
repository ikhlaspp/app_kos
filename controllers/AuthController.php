<?php
// File: nama_proyek_kos/controllers/AuthController.php

class AuthController extends BaseController {
    private UserModel $userModel;
    private ?LogAuditModel $logAuditModel = null; // Deklarasikan dan beri nilai awal null

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->userModel = new UserModel($this->pdo);

        // Inisialisasi LogAuditModel jika classnya ada
        if (class_exists('LogAuditModel')) {
            $this->logAuditModel = new LogAuditModel($this->pdo);
        } else {
            // Anda bisa mencatat ini jika penting, atau biarkan jika LogAuditModel opsional
            // error_log("Pemberitahuan: Class LogAuditModel tidak ditemukan, fitur log audit di AuthController tidak aktif.");
        }
    }

    /**
     * Menampilkan form login atau memproses data login.
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->setFlashMessage("Email dan password wajib diisi.", "error");
                $this->loadView('auth/login', ['email' => $email], 'Login'); // Pastikan view 'auth/login' ada
                return;
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                $logMessage = ($_SESSION['is_admin'] ? "Admin login: " : "User login: ") . htmlspecialchars($user['nama']);
                if (isset($this->logAuditModel)) {
                    $this->logAuditModel->addLog($logMessage, (int)$user['id']);
                }
                
                $this->setFlashMessage("Selamat datang kembali, " . htmlspecialchars($user['nama']) . "!", "success");

                if ($_SESSION['is_admin'] === true) {
                    $this->redirect('admin/dashboard');
                } else {
                    $this->redirect('user/dashboard'); 
                }
                return; // Ditambahkan setelah redirect
            } else {
                if (isset($this->logAuditModel)) {
                    $this->logAuditModel->addLog("Percobaan login gagal untuk email: " . htmlspecialchars($email), null);
                }
                $this->setFlashMessage("Email atau password salah.", "error");
                $this->loadView('auth/login', ['email' => $email], 'Login'); // Pastikan view 'auth/login' ada
            }
        } else {
            // Tampilkan form login (GET request)
            if(isset($_SESSION['user_id'])) { 
                if ($_SESSION['is_admin'] ?? false === true) {
                    $this->redirect('admin/dashboard'); 
                } else {
                    $this->redirect('user/dashboard'); 
                }
                return; // Ditambahkan setelah redirect
            }
            $this->loadView('auth/login', [], 'Login Pengguna'); // Pastikan view 'auth/login' ada
        }
    }

    /**
     * Menampilkan form registrasi atau memproses data registrasi.
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Menggunakan isset dan trim(strip_tags(...)) untuk sanitasi dasar
            $nama = isset($_POST['nama']) ? trim(strip_tags($_POST['nama'])) : '';
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // FILTER_SANITIZE_EMAIL masih ok
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $no_telepon_input = isset($_POST['no_telepon']) ? trim(strip_tags($_POST['no_telepon'])) : '';
            $no_telepon = !empty($no_telepon_input) ? $no_telepon_input : null;
            $alamat_input = isset($_POST['alamat']) ? trim(strip_tags($_POST['alamat'])) : '';
            $alamat = !empty($alamat_input) ? $alamat_input : null;

            $errors = [];
            if (empty($nama)) $errors[] = "Nama wajib diisi.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
            if (empty($password) || strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
            if ($password !== $password_confirm) $errors[] = "Konfirmasi password tidak cocok.";
            
            // Cek apakah email sudah terdaftar hanya jika format email valid
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && $this->userModel->getUserByEmail($email)) {
                $errors[] = "Email sudah terdaftar.";
            }

            if (!empty($errors)) {
                $this->setFlashMessage(implode("<br>", $errors), "error");
                $this->loadView('auth/register', $_POST, 'Registrasi Gagal'); // Kirim kembali input, pastikan view 'auth/register' ada
            } else {
                $userId = $this->userModel->registerUser($nama, $email, $password, $no_telepon, $alamat);
                if ($userId) {
                    if (isset($this->logAuditModel)) {
                        $this->logAuditModel->addLog("User baru terdaftar: " . htmlspecialchars($nama) . " (ID: {$userId})", (int)$userId);
                    }
                    $this->setFlashMessage("Registrasi berhasil! Silakan login dengan akun Anda.", "success");
                    $this->redirect('auth/login');
                    return; // Ditambahkan setelah redirect
                } else {
                    $this->setFlashMessage("Registrasi gagal. Terjadi kesalahan pada server.", "error");
                    $this->loadView('auth/register', $_POST, 'Registrasi Gagal'); // Pastikan view 'auth/register' ada
                }
            }
        } else {
            // Tampilkan form registrasi (GET request)
            if(isset($_SESSION['user_id'])) { 
                $this->redirect(''); // Arahkan ke halaman utama jika sudah login
                return; // Ditambahkan setelah redirect
            }
            $this->loadView('auth/register', [], 'Registrasi Pengguna Baru'); // Pastikan view 'auth/register' ada
        }
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(): void {
        $userIdForLog = $_SESSION['user_id'] ?? null; // Simpan sebelum session dihancurkan
        $userNameForLog = $_SESSION['user_nama'] ?? 'Unknown';

        $_SESSION = []; // Kosongkan array session

        // Hapus cookie session jika ada
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy(); // Hancurkan session

        if (isset($this->logAuditModel) && $userIdForLog) {
            $this->logAuditModel->addLog("User logout: " . htmlspecialchars($userNameForLog), (int)$userIdForLog);
        }

        $this->setFlashMessage("Anda telah berhasil logout.", "info");
        $this->redirect('auth/login'); 
        return; // Ditambahkan setelah redirect
    }
}
?>