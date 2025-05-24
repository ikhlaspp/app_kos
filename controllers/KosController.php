<?php

class KosController extends BaseController {
    private KosModel $kosModel;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->kosModel = new KosModel($this->pdo); // Inisialisasi KosModel
    }

    public function index(): void {
        $this->daftar();
    }

    public function daftar(): void {
        $pageTitle = "Daftar Kos Tersedia";
        $daftar_kos_db = $this->kosModel->getAllKos('harga_per_bulan', 'ASC'); // Ambil data dari model

        $data = [
            'daftar_kos' => $daftar_kos_db,
        ];
        $this->loadView('kos/daftar', $data, $pageTitle);
    }

    public function detail($id = null): void {
        if ($id === null) {
            $this->setFlashMessage("ID Kos tidak valid.", "error");
            $this->redirect('kos/daftar');
            return;
        }
        
        $kos_id_filtered = filter_var($id, FILTER_VALIDATE_INT);
        if ($kos_id_filtered === false || $kos_id_filtered <= 0) {
            $this->setFlashMessage("Format ID Kos tidak valid.", "error");
            $this->redirect('kos/daftar');
            return;
        }

        $kos_detail_db = $this->kosModel->getKosById($kos_id_filtered);

        if ($kos_detail_db === false || $kos_detail_db === null) {
            $this->setFlashMessage("Detail kos dengan ID '{$id}' tidak ditemukan.", "error");
            $this->redirect('kos/daftar');
            return;
        }
        
        $pageTitle = "Detail: " . htmlspecialchars($kos_detail_db['nama_kos']);
        $data = [
            'kos' => $kos_detail_db,
        ];
        $this->loadView('kos/detail', $data, $pageTitle);
    }
}
?>