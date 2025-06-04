<?php
// File: nama_proyek_kos/views/admin/kos/list.php

// Color Palette (sesuai yang Anda definisikan)
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#DCE6F5'; // A light, muted blue
$paletteMediumBlue = '#4682B4'; // A steel blue/medium blue
$paletteDarkBlue = '#192846';   // A very dark blue/navy

// Variabel ini diharapkan sudah di-pass dari AdminController
$pageTitle = $pageTitle ?? 'Kelola Data Kos';
// $appConfig (array) - sudah tersedia dari BaseController/AdminController
// $daftarKos (array) - Hasil kos yang sudah difilter dan dipaginasi
// $filterValues (array) - Untuk prefill form filter
// $pagination (object/array) - Berisi info pagination

$currentSearchTerm = $filterValues['search_term'] ?? '';
$currentKategori = $filterValues['kategori'] ?? '';
$currentMinHarga = $filterValues['min_harga'] ?? '';
$currentMaxHarga = $filterValues['max_harga'] ?? '';
$currentFilterStatus = $filterValues['status'] ?? '';
$currentFasilitas = $filterValues['fasilitas'] ?? '';

$currentPage = $pagination['currentPage'] ?? 1;
$totalPages = $pagination['totalPages'] ?? 1;
// $paginationBaseUrl akan menjadi $appConfig['BASE_URL'] . 'admin/kos'
// $filterQueryString akan berisi parameter filter yang aktif, misal: &status=available&kategori=putra
$paginationBaseUrl = $pagination['baseUrl'] ?? ($appConfig['BASE_URL'] . 'admin/kos'); 
$filterQueryString = $pagination['queryString'] ?? '';

// Untuk link pagination, kita perlu memastikan query string filter ditambahkan setelah ?page=X
// Jika filterQueryString sudah ada, ia akan dimulai dengan '&'
// Jika belum ada, dan kita mau tambahkan page, maka page jadi parameter pertama.
$pageQueryPrefix = !empty($filterQueryString) ? '&' : '?';
if (empty($filterQueryString) && strpos($paginationBaseUrl, '?') !== false) {
    // Jika baseUrl sudah punya query string lain (jarang terjadi untuk kasus ini)
    $pageQueryPrefix = '&';
} elseif (empty($filterQueryString)) {
    $pageQueryPrefix = '?'; // Jika tidak ada filter, page jadi parameter pertama
}


?>
<style>
    /* Tambahan style untuk modal dan responsivitas tabel jika perlu */
    .filter-modal-body { max-height: 75vh; overflow-y: auto; }
    .table-responsive-custom { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .pagination .page-item.active .page-link { z-index: 3; color: <?php echo htmlspecialchars($paletteWhite); ?>; background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;}
    .pagination .page-link { color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; }
    .pagination .page-link:hover { color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; }
    .btn-primary-custom { background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; color: <?php echo htmlspecialchars($paletteWhite); ?>; }
    .btn-primary-custom:hover { background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; }
    .btn-success-custom { background-color: #28a745; border-color: #28a745; color: <?php echo htmlspecialchars($paletteWhite); ?>; }
    .btn-success-custom:hover { background-color: #1f7a35; border-color: #1f7a35; }
    .btn-outline-secondary-custom { color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; border-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; }
    .btn-outline-secondary-custom:hover { background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; }

    /* Responsivitas untuk form filter di modal */
    @media (max-width: 767px) {
        .filter-modal-body .row .col-md-6 {
            margin-bottom: 1rem !important; /* Pastikan ada jarak di mobile */
        }
    }
</style>

<div style="font-family: 'Inter', sans-serif; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; padding: 20px; background-color: <?php echo htmlspecialchars($paletteWhite); ?>; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 1200px; margin: 20px auto;">
    <h2 style="text-align: center; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; margin-bottom: 20px;"><?php echo htmlspecialchars($pageTitle); ?></h2>

    <!-- Tombol Filter & Tambah -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
        <button type="button" class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#filterKosModalAdmin">
            <i class="fas fa-filter me-1"></i> Filter & Cari Kos
        </button>
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosCreate'); ?>" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: <?php echo htmlspecialchars($paletteWhite); ?>; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background-color 0.3s ease;">
            + Tambah Kos Baru
        </a>
    </div>
     <?php if (!empty($filterQueryString) && $filterQueryString !== '?'): ?>
        <div class="mb-3 text-end">
             <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" class="btn btn-sm btn-outline-secondary-custom">
                <i class="fas fa-times me-1"></i> Reset Semua Filter
            </a>
        </div>
    <?php endif; ?>


    <!-- Modal Filter -->
    <div class="modal fade" id="filterKosModalAdmin" tabindex="-1" aria-labelledby="filterKosModalAdminLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;">
                    <h5 class="modal-title" id="filterKosModalAdminLabel"><i class="fas fa-sliders-h me-2"></i>Filter & Pencarian Kos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" method="GET">
                    <div class="modal-body filter-modal-body">
                        <div class="mb-3">
                            <label for="search_term_modal_admin" class="form-label">Cari Nama atau Alamat Kos</label>
                            <input type="text" class="form-control" id="search_term_modal_admin" name="search_term" value="<?php echo htmlspecialchars($currentSearchTerm); ?>" placeholder="Masukkan nama atau area kos...">
                        </div>
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
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" class="btn btn-outline-secondary-custom">Reset</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary-custom"><i class="fas fa-check me-1"></i> Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($daftarKos)): ?>
        <div class="table-responsive-custom">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; background-color: <?php echo htmlspecialchars($paletteWhite); ?>; border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;">
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:left; border-top-left-radius: 8px;">ID</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:left;">Nama Kos</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:left;">Harga/Bulan</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;">Total Kamar</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;">Kamar Tersedia</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:left;">Status</th>
                        <th style="padding: 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center; border-top-right-radius: 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftarKos as $index => $kos): ?>
                    <tr style="background-color: <?php echo ($index % 2 == 0) ? $paletteWhite : '#FDFDFD'; ?>; border-bottom: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;">
                        <td style="padding: 10px 12px; border-left: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars($kos['id']); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars($kos['nama_kos']); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;">Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars(ucfirst($kos['status_kos'])); ?></td>
                        <td style="padding: 10px 12px; border-right: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id']); ?>" style="text-decoration:none; color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; margin-right:10px; font-weight: 600;">Edit</a>
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']); ?>" style="text-decoration:none; color: #DC3545; font-weight: 600;">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4 d-flex justify-content-center">
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
                    // Logic untuk menampilkan nomor halaman (misal, hanya beberapa nomor di sekitar halaman saat ini)
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
        <div class="alert alert-info mt-3" role="alert" style="background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; border-color: <?php echo htmlspecialchars($paletteMediumBlue);?>;">
            Tidak ada data kos yang cocok dengan filter atau pencarian Anda, atau belum ada data kos sama sekali.
        </div>
    <?php endif; ?>
    
    <p style="margin-top: 30px; text-align:center;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/dashboard'); ?>" class="btn btn-outline-secondary-custom">Kembali ke Dashboard Admin</a>
    </p>
</div>
