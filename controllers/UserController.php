<?php

class UserController extends BaseController {
    private UserModel $userModel;
    private BookingModel $bookingModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->userModel = new UserModel($this->pdo);
        $this->bookingModel = new BookingModel($this->pdo); // Tambahkan BookingModel
    }

    /**
     * Menampilkan halaman dashboard pengguna.
     */
    public function dashboard(): void {
        // Pastikan pengguna sudah login
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage("Anda harus login untuk mengakses halaman ini.", "error");
            $this->redirect('auth/login');
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $userData = $this->userModel->getUserById($userId); // Ambil data pengguna saat ini

        if (!$userData) {
            // Skenario aneh jika user_id di session ada tapi data tidak ada di DB
            $this->setFlashMessage("Data pengguna tidak ditemukan. Silakan login kembali.", "error");
            // Logout paksa jika data user tidak konsisten
            unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_email'], $_SESSION['is_admin']);
            $this->redirect('auth/login');
            return;
        }

        // Ambil riwayat pemesanan pengguna
        $bookingHistory = $this->bookingModel->getBookingsByUserId($userId);

        $pageTitle = "Dashboard Pengguna";
        $data = [
            'user' => $userData,
            'bookingHistory' => $bookingHistory,
        ];

        $this->loadView('user/dashboard', $data, $pageTitle);
    }

    /**
     * Menampilkan form edit profil dan memproses perubahannya.
     */
    public function editProfile(): void {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage("Anda harus login untuk mengakses halaman ini.", "error");
            $this->redirect('auth/login?redirect_to=user/editProfile');
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $pageTitle = "Edit Profil Saya";
        $viewName = 'user/edit_profile';

        // Ambil data pengguna saat ini dari database untuk ditampilkan atau jika tidak ada POST
        $currentUserData = $this->userModel->getUserById($userId);
        if (!$currentUserData) {
            $this->setFlashMessage("Gagal memuat data pengguna. Sesi mungkin tidak valid.", "error");
            // Logout paksa jika data pengguna tidak konsisten dengan session
            unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_email'], $_SESSION['is_admin']);
            $this->redirect('auth/login');
            return;
        }

        $dataForView = ['user' => $currentUserData]; // Data default untuk form (GET request)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form POST dan sanitasi dasar
            $nama = isset($_POST['nama']) ? trim(strip_tags($_POST['nama'])) : '';
            // no_telepon dan alamat boleh kosong (NULL), jadi perlakuan null jika string kosong
            $no_telepon_input = isset($_POST['no_telepon']) ? trim(strip_tags($_POST['no_telepon'])) : '';
            $no_telepon = !empty($no_telepon_input) ? $no_telepon_input : null;
            
            $alamat_input = isset($_POST['alamat']) ? trim(strip_tags($_POST['alamat'])) : '';
            $alamat = !empty($alamat_input) ? $alamat_input : null;

            $errors = [];
            if (empty($nama)) {
                $errors[] = "Nama tidak boleh kosong.";
            }
            // Tambahkan validasi lain jika perlu (misal format nomor telepon)

            if (empty($errors)) {
                if ($this->userModel->updateUserProfile($userId, $nama, $no_telepon, $alamat)) {
                    // Update nama di session jika berubah
                    if ($_SESSION['user_nama'] !== $nama) {
                        $_SESSION['user_nama'] = $nama;
                    }
                    $this->setFlashMessage("Profil berhasil diperbarui.", "success");
                    $this->redirect('user/dashboard'); // Redirect ke dashboard setelah sukses
                    return;
                } else {
                    $this->setFlashMessage("Gagal memperbarui profil. Terjadi kesalahan pada server.", "error");
                    // Jika gagal update DB, data untuk view adalah data POST agar pengguna bisa koreksi
                    $dataForView['user'] = array_merge($currentUserData, $_POST); // Gabungkan, POST menimpa
                    $dataForView['user']['email'] = $currentUserData['email']; // Pastikan email tidak terambil dari POST
                }
            } else {
                $this->setFlashMessage(implode("<br>", $errors), "error");
                // Jika ada error validasi, data untuk view adalah data POST
                $dataForView['user'] = array_merge($currentUserData, $_POST);
                $dataForView['user']['email'] = $currentUserData['email'];
            }
        }
        // Muat view dengan data yang sesuai (data dari DB untuk GET, data POST jika ada error di POST)
        $this->loadView($viewName, $dataForView, $pageTitle);
    }

    // Anda bisa menambahkan method lain di sini seperti:
    // public function profile() { ... }
    // public function editProfile() { ... }
    // public function bookingHistory() { ... } // Jika ingin halaman terpisah
}
?>