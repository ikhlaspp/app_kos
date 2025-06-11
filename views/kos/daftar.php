<?php

$paletteWhite      = '#FFFFFF';
$paletteLightBlue  = '#E9F1F7';
$paletteMediumBlue = '#4A90E2';
$paletteDarkBlue   = '#1A3A5B';
$paletteTextPrimary = '#0D2A57';
$paletteTextSecondary = '#555555';

$currentSearchTerm   = $filterValues['search_term'] ?? '';
$currentKategori     = $filterValues['kategori']    ?? '';
$currentMinHarga     = $filterValues['min_harga']   ?? '';
$currentMaxHarga     = $filterValues['max_harga']   ?? '';
$currentFilterStatus = $filterValues['status']      ?? '';
$currentFasilitas    = $filterValues['fasilitas']   ?? '';

$currentPage       = $pagination['currentPage']   ?? 1;
$totalPages        = $pagination['totalPages']    ?? 1;
$paginationBaseUrl = $pagination['baseUrl']       ?? ($appConfig['BASE_URL'] . 'kos/daftar');
$filterQueryString = $pagination['queryString']   ?? '';

?>
<style>
    .kos-item img {
        transition: transform 0.3s ease-in-out;
    }

    .kos-item:hover img {
        transform: scale(1.05);
    }

    .kos-item {
        border: none;
        border-radius: 12px;
        background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .kos-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .kos-item .card-img-top {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .kos-item .card-body-custom {
        padding: 18px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .kos-item .kos-title {
        margin-top: 0;
        margin-bottom: 8px;
        font-size: 1.25em;
        font-weight: 600;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        min-height: 2.8em;
        line-height: 1.4;
    }

    .kos-item .kos-title a {
        text-decoration: none;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        transition: color 0.2s ease;
    }

    .kos-item .kos-title a:hover {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
    }

    .kos-item .kos-location {
        font-size: 0.9em;
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
        margin-bottom: 8px;
        flex-grow: 1;
        line-height: 1.4;
    }

    .kos-item .kos-location i {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        margin-right: 5px;
    }

    .kos-item .kos-price {
        font-size: 1.15em;
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .kos-item .kos-price small {
        font-weight: 400;
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
    }

    .kos-item .kos-meta {
        font-size: 0.85em;
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kos-item .kos-meta i {
        margin-right: 4px;
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
    }

    .kos-item .kos-status {
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 5px;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        display: inline-block;
    }

    .kos-item .btn-detail {
        display: block;
        width: 100%;
        text-align: center;
        padding: 12px 0;
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        text-decoration: none;
        border-radius: 0 0 12px 12px;
        font-size: 1em;
        font-weight: 600;
        transition: background-color 0.2s ease;
        margin-top: auto;
    }

    .kos-item .btn-detail:hover {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }

    .sidebar-filter {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
        align-self: flex-start;
        z-index: 100;
    }

    .sidebar-filter .form-label {
        font-weight: 600;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        margin-bottom: 0.5rem;
    }

    .btn-apply-filter, .btn-reset-filter {
        font-weight: 600;
        width: 100%;
        margin-bottom: 10px;
    }

    .btn-apply-filter {
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }

    .btn-apply-filter:hover {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }

    .main-content {
        padding-left: 20px;
    }

    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .main-search-form .input-group {
        width: 100%;
    }

    .main-search-form .btn-search {
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        font-weight: 600;
    }

    .main-search-form .btn-search:hover {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    }

    @media (max-width: 767.98px) {
        .sidebar-filter {
            position: static;
            width: 100%;
            margin-bottom: 20px;
            top: auto;
            padding-bottom: 1px;
        }
        .main-content {
            padding-left: 0;
        }
        .btn-apply-filter, .btn-reset-filter {
            width: auto;
            display: inline-block;
            margin-right: 10px;
        }
        .sidebar-filter .d-grid {
            display: flex !important;
            justify-content: center;
        }

        .kos-item .card-body-custom {
            padding: 15px;
        }
        .kos-item .kos-title {
            font-size: 1.1em;
        }
        .kos-item .kos-price {
            font-size: 1.05em;
        }
    }
</style>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Daftar Kos Tersedia'); ?></h2>
</div>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" method="GET" class="mb-4 main-search-form">
    <div class="input-group">
        <input type="text" class="form-control" name="search_term" value="<?php echo htmlspecialchars($currentSearchTerm); ?>" placeholder="Cari nama atau alamat kos..." aria-label="Cari Kos">
        <button class="btn btn-search" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
        <?php
        $hasActiveFilters = !empty($currentSearchTerm) ||
                            !empty($currentKategori) ||
                            !empty($currentMinHarga) ||
                            !empty($currentMaxHarga) ||
                            !empty($currentFilterStatus) ||
                            !empty($currentFasilitas);
        if ($hasActiveFilters):
        ?>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" class="btn btn-outline-secondary" title="Reset Semua Pencarian & Filter">
                <i class="fas fa-times"></i>
            </a>
        <?php endif; ?>
    </div>
</form>

<div class="row">
    <div class="col-md-3">
        <div class="sidebar-filter">
            <h5 class="mb-3 text-center"><i class="fas fa-sliders-h me-2"></i>Opsi Filter Lanjutan</h5>
            <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" method="GET">
                <?php if (!empty($currentSearchTerm)): ?>
                    <input type="hidden" name="search_term" value="<?php echo htmlspecialchars($currentSearchTerm); ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="filter_kategori" class="form-label">Kategori Kos</label>
                    <select class="form-select" id="filter_kategori" name="kategori">
                        <option value="">Semua Kategori</option>
                        <option value="putra" <?php echo ($currentKategori === 'putra') ? 'selected' : ''; ?>>Putra</option>
                        <option value="putri" <?php echo ($currentKategori === 'putri') ? 'selected' : ''; ?>>Putri</option>
                        <option value="campur" <?php echo ($currentKategori === 'campur') ? 'selected' : ''; ?>>Campur</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="filter_status" class="form-label">Ketersediaan</label>
                    <select class="form-select" id="filter_status" name="status">
                        <option value="">Semua Status</option>
                        <option value="available" <?php echo ($currentFilterStatus === 'available') ? 'selected' : ''; ?>>Tersedia (Available)</option>
                        <option value="booked" <?php echo ($currentFilterStatus === 'booked') ? 'selected' : ''; ?>>Sudah Dipesan (Booked)</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-12 mb-3">
                        <label for="min_harga" class="form-label">Harga Minimum (Rp)</label>
                        <input type="number" class="form-control" id="min_harga" name="min_harga" value="<?php echo htmlspecialchars($currentMinHarga); ?>" placeholder="Contoh: 500000" step="50000">
                    </div>
                    <div class="col-12">
                        <label for="max_harga" class="form-label">Harga Maksimum (Rp)</label>
                        <input type="number" class="form-control" id="max_harga" name="max_harga" value="<?php echo htmlspecialchars($currentMaxHarga); ?>" placeholder="Contoh: 2000000" step="50000">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="filter_fasilitas" class="form-label">Fasilitas (pisahkan dengan koma)</label>
                    <input type="text" class="form-control" id="filter_fasilitas" name="fasilitas" value="<?php echo htmlspecialchars($currentFasilitas); ?>" placeholder="Contoh: AC, WiFi, Kamar Mandi Dalam">
                    <small class="form-text text-muted">Cari kos yang memiliki salah satu atau semua fasilitas yang disebutkan (tergantung implementasi backend).</small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-apply-filter"><i class="fas fa-check me-1"></i> Terapkan Filter</button>
                    <?php
                    $otherFiltersActive = !empty($currentKategori) ||
                                          !empty($currentMinHarga) ||
                                          !empty($currentMaxHarga) ||
                                          !empty($currentFilterStatus) ||
                                          !empty($currentFasilitas);
                    if ($otherFiltersActive):
                    ?>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar' . (!empty($currentSearchTerm) ? '?search_term=' . urlencode($currentSearchTerm) : '')); ?>" class="btn btn-outline-secondary btn-reset-filter">
                            <i class="fas fa-times me-1"></i> Reset Filter Lainnya
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-9 main-content">
        <?php if (isset($appConfig) && isset($daftarKos)): ?>
            <?php if (!empty($daftarKos)): ?>
                <div class="kos-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 0;">
                    <?php foreach ($daftarKos as $item_kos): ?>
                        <div class="kos-item">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" style="text-decoration: none; color: inherit; display:block;">
                                <?php if (!empty($item_kos['gambar_utama'])): ?>
                                    <img src="<?php echo htmlspecialchars($appConfig['UPLOADS_URL_PATH'] . $item_kos['gambar_utama']); ?>"
                                        alt="Gambar <?php echo htmlspecialchars($item_kos['nama_kos']); ?>"
                                        class="card-img-top">
                                <?php else: ?>
                                    <div class="card-img-top" style="background-color: #f0f0f0; display:flex; align-items:center; justify-content:center; color:#aaa;">
                                        <span>Tidak ada gambar</span>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="card-body-custom">
                                <h3 class="kos-title">
                                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>">
                                        <?php echo htmlspecialchars($item_kos['nama_kos']); ?>
                                    </a>
                                </h3>
                                <p class="kos-location"><small><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item_kos['alamat']); ?></small></p>
                                <p class="kos-price">Rp <?php echo number_format($item_kos['harga_per_bulan'], 0, ',', '.'); ?> <small>/ bulan</small></p>
                                <div class="kos-meta">
                                    <span><i class="fas fa-bed"></i> <?php echo htmlspecialchars($item_kos['jumlah_kamar_tersedia'] ?? 0); ?> / <?php echo htmlspecialchars($item_kos['jumlah_kamar_total'] ?? 0); ?> kamar</span>
                                    <span>
                                        Status:
                                        <?php
                                        $statusValue = $item_kos['status_kos'] ?? 'maintenance';
                                        $bgColor = '#6c757d';
                                        $textColor = 'white';
                                        switch ($statusValue) {
                                            case 'available':   $bgColor = '#28a745'; break;
                                            case 'booked':      $bgColor = '#dc3545'; break;
                                            case 'maintenance': $bgColor = '#ffc107'; $textColor = '#212529'; break;
                                        }
                                        ?>
                                        <span class="kos-status" style="background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>;">
                                            <?php echo ucfirst(htmlspecialchars($item_kos['status_kos'] ?? 'N/A')); ?>
                                        </span>
                                    </span>
                                </div>
                                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" class="btn-detail">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . '/page/' . ($currentPage - 1) . $filterQueryString); ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . '/page/' . $i . $filterQueryString); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . '/page/' . ($currentPage + 1) . $filterQueryString); ?>" aria-label="Next">
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
        <?php else: ?>
            <p class="alert alert-warning">Data konfigurasi aplikasi atau data kos tidak tersedia untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
</div>