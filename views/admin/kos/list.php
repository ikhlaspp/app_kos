<?php
// File: views/admin/kos/list.php

// Variables passed from AdminController (kept as they are for functionality)
$pageTitle = $pageTitle ?? 'Kelola Data Kos';
$currentSearchTerm = $filterValues['search_term'] ?? '';
$currentKategori = $filterValues['kategori'] ?? '';
$currentMinHarga = $filterValues['min_harga'] ?? '';
$currentMaxHarga = $filterValues['max_harga'] ?? '';
$currentFilterStatus = $filterValues['status'] ?? '';
$currentFasilitas = $filterValues['fasilitas'] ?? '';

$currentPage = $pagination['currentPage'] ?? 1;
$totalPages = $pagination['totalPages'] ?? 1;
$paginationBaseUrl = $pagination['baseUrl'] ?? ($appConfig['BASE_URL'] . 'admin/kos');
$filterQueryString = $pagination['queryString'] ?? '';

// For link pagination: ensure query string filter is added after ?page=X
$pageQueryPrefix = !empty($filterQueryString) ? '&' : '?';
if (empty($filterQueryString) && strpos($paginationBaseUrl, '?') !== false) {
    $pageQueryPrefix = '&';
} elseif (empty($filterQueryString)) {
    $pageQueryPrefix = '?';
}

// Ensure $daftarKos is defined for display purposes, even if empty
if (!isset($daftarKos)) {
    $daftarKos = []; // Default to empty array to avoid PHP errors if not passed
}

?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
</div>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" class="form-control" name="search_term" placeholder="Cari nama atau alamat kos..." value="<?php echo htmlspecialchars($currentSearchTerm); ?>">
        <button class="btn btn-primary" type="submit">Cari</button>
        <?php if (!empty($currentSearchTerm)): ?>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos?' . http_build_query(array_diff_key($_GET, ['search_term' => '']))); ?>" class="btn btn-outline-secondary">x</a>
        <?php endif; ?>
    </div>
    <?php
    // Preserve other filter parameters when submitting search
    foreach ($_GET as $key => $value) {
        if ($key !== 'search_term' && $key !== 'page' && !is_array($value)) {
            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        } elseif (is_array($value)) { // Handle array parameters if any, though unlikely for this form
             foreach ($value as $val) {
                 echo '<input type="hidden" name="' . htmlspecialchars($key) . '[]" value="' . htmlspecialchars($val) . '">';
             }
        }
    }
    ?>
</form>


<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterKosModalAdmin">
        <i class="fas fa-filter me-1"></i> Filter Lanjutan
    </button>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosCreate'); ?>" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Tambah Kos Baru
    </a>
    <?php
    // Check if any filter is active (excluding 'search_term' which has its own reset)
    $hasActiveFilters = false;
    foreach ($_GET as $key => $value) {
        if (!empty($value) && $key !== 'search_term' && $key !== 'page') {
            $hasActiveFilters = true;
            break;
        }
    }
    if ($hasActiveFilters): ?>
        <!-- <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos?' . http_build_query(array_intersect_key($_GET, ['search_term' => '']))); ?>" class="btn btn-outline-secondary btn-sm">
             <i class="fas fa-times me-1"></i> Reset Filter Lanjutan
        </a> -->
    <?php endif; ?>
</div>

<div class="modal fade" id="filterKosModalAdmin" tabindex="-1" aria-labelledby="filterKosModalAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="filterKosModalAdminLabel"><i class="fas fa-sliders-h me-2"></i>Opsi Filter Kos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" method="GET">
                <div class="modal-body">
                    <?php
                    // Preserve search term when submitting modal filters
                    if (!empty($currentSearchTerm)) {
                        echo '<input type="hidden" name="search_term" value="' . htmlspecialchars($currentSearchTerm) . '">';
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_kategori_modal_admin" class="form-label">Kategori Kos</label>
                            <select class="form-select" id="filter_kategori_modal_admin" name="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="putra" <?php echo ($currentKategori === 'putra') ? 'selected' : ''; ?>>Putra</option>
                                <option value="putri" <?php echo ($currentKategori === 'putri') ? 'selected' : ''; ?>>Putri</option>
                                <option value="campur" <?php echo ($currentKategori === 'campur') ? 'selected' : ''; ?>>Campur</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_status_modal_admin" class="form-label">Ketersediaan</label>
                            <select class="form-select" id="filter_status_modal_admin" name="status">
                                <option value="">Semua Status</option>
                                <option value="available" <?php echo ($currentFilterStatus === 'available') ? 'selected' : ''; ?>>Available</option>
                                <option value="booked" <?php echo ($currentFilterStatus === 'booked') ? 'selected' : ''; ?>>Booked</option>
                                <option value="maintenance" <?php echo ($currentFilterStatus === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_harga_modal_admin" class="form-label">Harga Minimum (Rp)</label>
                            <input type="number" class="form-control" id="min_harga_modal_admin" name="min_harga" value="<?php echo htmlspecialchars($currentMinHarga); ?>" placeholder="Contoh: 500000" step="50000">
                        </div>
                        <div class="col-md-6">
                            <label for="max_harga_modal_admin" class="form-label">Harga Maksimum (Rp)</label>
                            <input type="number" class="form-control" id="max_harga_modal_admin" name="max_harga" value="<?php echo htmlspecialchars($currentMaxHarga); ?>" placeholder="Contoh: 2000000" step="50000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="filter_fasilitas_modal_admin" class="form-label">Fasilitas (pisahkan dengan koma)</label>
                        <input type="text" class="form-control" id="filter_fasilitas_modal_admin" name="fasilitas" value="<?php echo htmlspecialchars($currentFasilitas); ?>" placeholder="Contoh: AC, WiFi, Kamar Mandi Dalam">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos?' . http_build_query(array_intersect_key($_GET, ['search_term' => '']))); ?>" class="btn btn-outline-secondary">Reset Filter</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> Terapkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($daftarKos)): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr class="table-header-custom">
                    <th>ID</th>
                    <th>Nama Kos</th>
                    <th>Harga/Bulan</th>
                    <th class="text-center">Total Kamar</th>
                    <th class="text-center">Kamar Tersedia</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftarKos as $index => $kos): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($kos['id']); ?></td>
                    <td data-label="Nama Kos"><?php echo htmlspecialchars($kos['nama_kos']); ?></td>
                    <td data-label="Harga/Bulan">Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?></td>
                    <td data-label="Total Kamar" class="text-center"><?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?></td>
                    <td data-label="Kamar Tersedia" class="text-center"><?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?></td>
                    <td data-label="Status">
                        <?php
                            $statusValue = $kos['status_kos'] ?? 'maintenance';
                            $badgeClass = '';
                            switch ($statusValue) {
                                case 'available':   $badgeClass = 'bg-success'; break;
                                case 'booked':      $badgeClass = 'bg-danger'; break;
                                case 'maintenance': $badgeClass = 'bg-warning text-dark'; break;
                                default: $badgeClass = 'bg-secondary'; break;
                            }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(htmlspecialchars($statusValue)); ?></span>
                    </td>
                    <td data-label="Aksi" class="text-center">
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']); ?>" class="btn btn-sm btn-danger" >Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="d-flex justify-content-center mt-3">
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . $pageQueryPrefix . 'page=' . ($currentPage - 1) . $filterQueryString); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                if ($startPage > 1) {
                    echo '<li class="page-item"><a class="page-link" href="'.htmlspecialchars($paginationBaseUrl . $pageQueryPrefix . 'page=1' . $filterQueryString).'">1</a></li>';
                    if ($startPage > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . $pageQueryPrefix . 'page=' . $i . $filterQueryString); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . $pageQueryPrefix . 'page=' . $totalPages . $filterQueryString); ?>"><?php echo $totalPages; ?></a></li>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . $pageQueryPrefix . 'page=' . ($currentPage + 1) . $filterQueryString); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info mt-3" role="alert">
        Tidak ada data kos yang cocok dengan filter atau pencarian Anda, atau belum ada data kos sama sekali.
    </div>
<?php endif; ?>

<!-- <p class="mt-3 text-center">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/dashboard'); ?>" class="btn btn-outline-secondary">Kembali ke Dashboard Admin</a>
</p> -->