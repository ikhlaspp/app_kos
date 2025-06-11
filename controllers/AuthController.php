<?php

class AuthController extends BaseController {
    private UserModel $userModel;
    private LogAuditModel $logAuditModel;
    private VoucherModel $voucherModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->userModel = new UserModel($this->pdo);
        if (class_exists('LogAuditModel')) {
            $this->logAuditModel = new LogAuditModel($this->pdo);
        } else {
            error_log("Warning: LogAuditModel class not found in AuthController. Audit logging features will be inactive.");
        }
        $this->voucherModel = new VoucherModel($this->pdo);
    }

    public function index(): void {
        $this->login();
    }

    public function login(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }

        $pageTitle = "Login Akun";
        $data = ['email' => $this->getInputGet('email', '')];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->getInputPost('email', null, FILTER_SANITIZE_EMAIL);
            $password = $this->getInputPost('password');

            if (empty($email) || empty($password)) {
                $this->setFlashMessage("Email dan password wajib diisi.", "error");
                $data['email'] = htmlspecialchars($email);
                $this->loadView('auth/login', $data, $pageTitle);
                return;
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];

                $this->userModel->updateUserLastActive($user['id']);
                if (isset($this->logAuditModel)) {
                    $userIdForLog = (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) ? (int)$_SESSION['user_id'] : null;
                    $this->logAuditModel->addLog("User login: {$user['nama']}", $userIdForLog);
                }

                $redirectTo = $this->getInputGet('redirect_to', '');
                if (!empty($redirectTo)) {
                    $this->redirect($redirectTo);
                } elseif ($_SESSION['is_admin']) {
                    $this->redirect('admin/dashboard');
                } else {
                    $this->redirect('');
                }
                return;
            } else {
                $this->setFlashMessage("Email atau password salah.", "error");
                if (isset($this->logAuditModel)) {
                    $this->logAuditModel->addLog("Percobaan login gagal untuk email: " . htmlspecialchars($email), null);
                }
                $data['email'] = htmlspecialchars($email);
                $this->loadView('auth/login', $data, $pageTitle);
                return;
            }
        }
        $this->loadView('auth/login', $data, $pageTitle);
    }

    public function logout(): void {
        if ($this->isLoggedIn()) {
            $logUserId = (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) ? (int)$_SESSION['user_id'] : null;
            $logUserName = $_SESSION['user_nama'] ?? 'Unknown';
            if (isset($this->logAuditModel)) {
                $this->logAuditModel->addLog("User logout: {$logUserName}", $logUserId);
            }
        }
        session_unset();
        session_destroy();
        $_SESSION = [];
        $this->redirect('auth/login');
    }

    public function register(): void {
        $pageTitle = "Daftar Akun Baru";

        if ($this->isLoggedIn()) {
            $this->redirect('');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $this->getInputPost('nama', null, FILTER_SANITIZE_SPECIAL_CHARS);
            $email = $this->getInputPost('email', null, FILTER_SANITIZE_EMAIL);
            $password = $this->getInputPost('password');
            $confirmPassword = $this->getInputPost('confirm_password');
            $no_telepon = $this->getInputPost('no_telepon', null, FILTER_SANITIZE_SPECIAL_CHARS);
            $alamat = $this->getInputPost('alamat', null, FILTER_SANITIZE_SPECIAL_CHARS);

            $dataForView = [
                'nama' => htmlspecialchars($nama),
                'email' => htmlspecialchars($email),
                'no_telepon' => htmlspecialchars($no_telepon),
                'alamat' => htmlspecialchars($alamat),
            ];

            if (empty($nama) || empty($email) || empty($password) || empty($confirmPassword)) {
                $this->setFlashMessage("Semua field wajib diisi.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
                return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setFlashMessage("Format email tidak valid.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
                return;
            } elseif ($password !== $confirmPassword) {
                $this->setFlashMessage("Konfirmasi password tidak cocok.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
                return;
            } elseif (strlen($password) < 6) {
                $this->setFlashMessage("Password minimal 6 karakter.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
                return;
            }

            if ($this->userModel->getUserByEmail($email)) {
                $this->setFlashMessage("Email ini sudah terdaftar.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
                return;
            }

            $userId = $this->userModel->registerUser($nama, $email, $password, $no_telepon, $alamat);

            if ($userId) {
                // --- MODIFIED: REMOVE AUTO-CLAIM FOR NEW USER VOUCHER ---
                // The new user voucher (is_claimable_by_new_users = 1) will now
                // appear in the 'Available Vouchers for Claiming' section,
                // and the user must explicitly click 'Claim'.
                // $newUserVoucher = $this->voucherModel->getVoucherByCode('NEWUSER10');
                // if ($newUserVoucher && $newUserVoucher['is_claimable_by_new_users'] == 1) {
                //     $claimSuccess = $this->voucherModel->claimVoucher($userId, $newUserVoucher['id'], 'available_to_claim');
                //     if (!$claimSuccess) {
                //         error_log("Failed to auto-assign NEWUSER10 voucher to new user ID: {$userId}");
                //     }
                // } else {
                //     error_log("NEWUSER10 voucher not found or not configured for new users.");
                // }
                // --- END MODIFIED ---

                $this->setFlashMessage("Registrasi berhasil! Silakan login. Anda mungkin memiliki voucher baru yang bisa diklaim di dashboard Anda.", "success");
                if (isset($this->logAuditModel)) {
                    $this->logAuditModel->addLog("User baru terdaftar: {$nama} (ID: {$userId})", (int)$userId, json_encode(['user_id' => (int)$userId]));
                }
                $this->redirect('auth/login');
            } else {
                $this->setFlashMessage("Registrasi gagal. Silakan coba lagi.", "error");
                $this->loadView('auth/register', $dataForView, $pageTitle);
            }
        } else {
            $this->loadView('auth/register', [], $pageTitle);
        }
    }
}