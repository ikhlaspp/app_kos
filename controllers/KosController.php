<?php

class KosController extends BaseController {
    private KosModel $kosModel;
    private int $itemsPerPage = 8;

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->kosModel = new KosModel($this->pdo);
    }

    public function index(): void {
        $this->daftar();
    }

    public function daftar(): void {
        $pageTitle = "Daftar Kos Tersedia";

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $this->itemsPerPage;

        $filterValues = [
            'search_term' => $this->getInputGet('search_term', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'kategori'    => $this->getInputGet('kategori', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'min_harga'   => $this->getInputGet('min_harga', null, FILTER_VALIDATE_FLOAT),
            'max_harga'   => $this->getInputGet('max_harga', null, FILTER_VALIDATE_FLOAT),
            'status'      => $this->getInputGet('status', null, FILTER_SANITIZE_SPECIAL_CHARS),
            'fasilitas'   => $this->getInputGet('fasilitas', null, FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        $daftarKosDb = $this->kosModel->getAllKos($filterValues, 'harga_per_bulan', 'ASC', $this->itemsPerPage, $offset);
        $totalFilteredKos = $this->kosModel->countAllKosFiltered($filterValues);

        $totalPages = ceil($totalFilteredKos / $this->itemsPerPage);
        if ($totalPages < 1) {
            $totalPages = 1;
        }
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $filterParamsForUrl = array_filter($filterValues);
        $filterQueryString = '';
        if (!empty($filterParamsForUrl)) {
            $filterQueryString = '&' . http_build_query($filterParamsForUrl);
        }

        $paginationBaseUrl = $this->appConfig['BASE_URL'] . 'kos/daftar';

        $data = [
            'daftarKos'    => $daftarKosDb,
            'filterValues' => $filterValues,
            'pagination'   => [
                'currentPage' => $currentPage,
                'totalPages'  => $totalPages,
                'baseUrl'     => $paginationBaseUrl,
                'queryString' => $filterQueryString,
            ]
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
        $data = [ 'kos' => $kos_detail_db ];
        $this->loadView('kos/detail', $data, $pageTitle);
    }
}