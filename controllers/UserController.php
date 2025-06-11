<?php

class UserController extends BaseController {
    private UserModel $userModel;
    private BookingModel $bookingModel;
    private VoucherModel $voucherModel; // ADDED: VoucherModel property

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo);
        $this->voucherModel = new VoucherModel($this->pdo); // ADDED: Initialize VoucherModel
    }

    /**
     * Displays the user dashboard page, including user info, bookings, and vouchers.
     *
     * @return void
     */
    public function dashboard(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlashMessage("Anda harus login untuk mengakses dashboard.", "warning");
            $this->redirect('auth/login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $pageTitle = "Dashboard Pengguna";

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->setFlashMessage("Data pengguna tidak ditemukan. Silakan login kembali.", "error");
            unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_email'], $_SESSION['is_admin']);
            $this->redirect('auth/login');
            return;
        }

        $bookings = $this->bookingModel->getBookingsByUserId($userId); // Get user's bookings

        // ADDED: Fetch vouchers for dashboard
        $availableVouchers = $this->voucherModel->getAvailableVouchersForClaiming($userId);
        $claimedVouchers = $this->voucherModel->getUserClaimedVouchers($userId);
        // END ADDED

        $data = [
            'user' => $user,
            'bookings' => $bookings,
            'availableVouchers' => $availableVouchers, // ADDED: Pass to view
            'claimedVouchers' => $claimedVouchers,     // ADDED: Pass to view
        ];

        $this->loadView('user/dashboard', $data, $pageTitle);
    }

    /**
     * Displays a form to edit the user's profile and processes form submission.
     *
     * @return void
     */
    public function editProfile(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlashMessage("Anda harus login untuk mengakses halaman ini.", "error");
            $this->redirect('auth/login?redirect_to=user/editProfile');
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $pageTitle = "Edit Profil Saya";
        $viewName = 'user/edit_profile';

        $currentUserData = $this->userModel->getUserById($userId);
        if (!$currentUserData) {
            $this->setFlashMessage("Gagal memuat data pengguna. Sesi mungkin tidak valid.", "error");
            unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_email'], $_SESSION['is_admin']);
            $this->redirect('auth/login');
            return;
        }

        $dataForView = ['user' => $currentUserData];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = isset($_POST['nama']) ? trim(strip_tags($_POST['nama'])) : '';
            $no_telepon_input = isset($_POST['no_telepon']) ? trim(strip_tags($_POST['no_telepon'])) : '';
            $no_telepon = !empty($no_telepon_input) ? $no_telepon_input : null;
            
            $alamat_input = isset($_POST['alamat']) ? trim(strip_tags($_POST['alamat'])) : '';
            $alamat = !empty($alamat_input) ? $alamat_input : null;

            $errors = [];
            if (empty($nama)) {
                $errors[] = "Nama tidak boleh kosong.";
            }

            if (empty($errors)) {
                if ($this->userModel->updateUserProfile($userId, $nama, $no_telepon, $alamat)) {
                    if ($_SESSION['user_nama'] !== $nama) {
                        $_SESSION['user_nama'] = $nama;
                    }
                    $this->setFlashMessage("Profil berhasil diperbarui.", "success");
                    $this->redirect('user/dashboard');
                    return;
                } else {
                    $this->setFlashMessage("Gagal memperbarui profil. Terjadi kesalahan pada server.", "error");
                    $dataForView['user'] = array_merge($currentUserData, $_POST);
                    $dataForView['user']['email'] = $currentUserData['email'];
                }
            } else {
                $this->setFlashMessage(implode("<br>", $errors), "error");
                $dataForView['user'] = array_merge($currentUserData, $_POST);
                $dataForView['user']['email'] = $currentUserData['email'];
            }
        }
        $this->loadView($viewName, $dataForView, $pageTitle);
    }

    /**
     * Handles the user's request to claim a voucher.
     * Accessed via POST request from the user dashboard.
     *
     * @param int|null $voucherId The ID of the voucher to claim.
     * @return void Redirects back to dashboard with flash message.
     */
    public function claimVoucher($voucherId = null): void {
        if (!$this->isLoggedIn()) {
            $this->setFlashMessage("Anda harus login untuk mengklaim voucher.", "warning");
            $this->redirect('auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $voucherId === null) {
            $this->setFlashMessage("Permintaan tidak valid.", "error");
            $this->redirect('user/dashboard');
            return;
        }

        $userId = $_SESSION['user_id'];
        $voucherId = filter_var($voucherId, FILTER_VALIDATE_INT);

        if (!$voucherId) {
            $this->setFlashMessage("ID Voucher tidak valid.", "error");
            $this->redirect('user/dashboard');
            return;
        }

        $voucher = $this->voucherModel->getVoucherById($voucherId);

        // Validate voucher existence and eligibility
        if (!$voucher || $voucher['is_active'] == 0 || strtotime($voucher['expiration_date']) < time()) {
            $this->setFlashMessage("Voucher tidak ditemukan, tidak aktif, atau sudah kadaluarsa.", "error");
            $this->redirect('user/dashboard');
            return;
        }

        // Check overall usage limit
        if ($voucher['total_usage_limit'] !== null && $voucher['current_total_uses'] >= $voucher['total_usage_limit']) {
            $this->setFlashMessage("Voucher ini sudah mencapai batas penggunaan maksimum.", "error");
            $this->redirect('user/dashboard');
            return;
        }

        // Check if user already has this specific voucher assigned/claimed
        $userVoucher = $this->voucherModel->getUserVoucher($userId, $voucherId);
        if ($userVoucher && ($userVoucher['status'] === 'claimed' || $userVoucher['status'] === 'used')) {
             $this->setFlashMessage("Anda sudah mengklaim voucher ini sebelumnya.", "info");
             $this->redirect('user/dashboard');
             return;
        }

        // Attempt to claim
        // If it was 'available_to_claim' (e.g. for new user), update its status to 'claimed'.
        // If it was not yet in user_vouchers, claim it as 'claimed'.
        if ($userVoucher && $userVoucher['status'] === 'available_to_claim') {
            $updateSql = "UPDATE user_vouchers SET status = 'claimed' WHERE user_id = :user_id AND voucher_id = :voucher_id";
            $stmt = $this->pdo->prepare($updateSql);
            $success = $stmt->execute([':user_id' => $userId, ':voucher_id' => $voucherId]);
            $message = "Voucher '{$voucher['name']}' berhasil diaktifkan!";
        } else {
            $success = $this->voucherModel->claimVoucher($userId, $voucherId, 'claimed');
            $message = "Voucher '{$voucher['name']}' berhasil diklaim!";
        }
        
        if ($success) {
            $this->setFlashMessage($message, "success");
        } else {
            $this->setFlashMessage("Gagal mengklaim voucher. Mungkin sudah diklaim atau ada masalah sistem.", "error");
        }
        $this->redirect('user/dashboard');
    }
}