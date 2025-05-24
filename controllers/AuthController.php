<?php

class AuthController extends BaseController {
    private UserModel $userModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->userModel = new UserModel($this->pdo); // Inisialisasi UserModel
    }

    /**
     * Menampilkan form login atau memproses data login.
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil input dengan filter dasar
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? ''; // Password tidak di-sanitize agar bisa diverifikasi

            if (empty($email) || empty($password)) {
                $this->setFlashMessage("Email dan password wajib diisi.", "error");
                $this->loadView('auth/login', ['email' => $email], 'Login');
                return;
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil, set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                $this->setFlashMessage("Selamat datang kembali, " . htmlspecialchars($user['nama']) . "!", "success");
                $this->redirect(''); // Redirect ke halaman utama/dashboard
            } else {
                $this->setFlashMessage("Email atau password salah.", "error");
                $this->loadView('auth/login', ['email' => $email], 'Login');
            }
        } else {
            // Tampilkan form login (GET request)
            if(isset($_SESSION['user_id'])) { // Jika sudah login, redirect ke home
                $this->redirect('');
                return;
            }
            $this->loadView('auth/login', [], 'Login Pengguna');
        }
    }

    /**
     * Menampilkan form registrasi atau memproses data registrasi.
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $no_telepon = filter_input(INPUT_POST, 'no_telepon', FILTER_SANITIZE_STRING);
            $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);

            $errors = [];
            if (empty($nama)) $errors[] = "Nama wajib diisi.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
            if (empty($password) || strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
            if ($password !== $password_confirm) $errors[] = "Konfirmasi password tidak cocok.";
            if ($this->userModel->getUserByEmail($email)) $errors[] = "Email sudah terdaftar.";

            if (!empty($errors)) {
                $this->setFlashMessage(implode("<br>", $errors), "error");
                $this->loadView('auth/register', $_POST, 'Registrasi Gagal'); // Kirim kembali input
            } else {
                $userId = $this->userModel->registerUser($nama, $email, $password, $no_telepon, $alamat);
                if ($userId) {
                    $this->setFlashMessage("Registrasi berhasil! Silakan login.", "success");
                    $this->redirect('auth/login');
                } else {
                    $this->setFlashMessage("Registrasi gagal. Terjadi kesalahan pada server.", "error");
                    $this->loadView('auth/register', $_POST, 'Registrasi Gagal');
                }
            }
        } else {
            // Tampilkan form registrasi (GET request)
            if(isset($_SESSION['user_id'])) { // Jika sudah login, redirect ke home
                $this->redirect('');
                return;
            }
            $this->loadView('auth/register', [], 'Registrasi Pengguna Baru');
        }
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(): void {
        // Hapus semua variabel session
        $_SESSION = [];

        // Hancurkan session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        $this->setFlashMessage("Anda telah berhasil logout.", "info");
        $this->redirect('auth/login'); // Atau redirect ke halaman utama
    }
}
?>