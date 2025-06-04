<?php
// ---- AWAL BLOK DEBUG ----
// Tambahkan ini untuk melihat variabel yang di-pass dari controller
echo "<div style='background-color: #fffacd; color: #333; padding: 15px; border: 2px solid #ffd700; margin-bottom: 20px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; position: relative; z-index: 9999;'>";
echo "<strong>DEBUG DATA DI views/kos/daftar.php:</strong><br><br>";

echo "<strong>Isi \$pageTitle:</strong><br>";
var_dump($pageTitle ?? 'Belum di-set oleh Controller');
echo "<hr>";

echo "<strong>Isi \$appConfig (hanya beberapa kunci penting):</strong><br>";
if (isset($appConfig) && is_array($appConfig)) {
    echo "BASE_URL: " . htmlspecialchars($appConfig['BASE_URL'] ?? 'TIDAK ADA') . "<br>";
    echo "UPLOADS_URL_PATH: " . htmlspecialchars($appConfig['UPLOADS_URL_PATH'] ?? 'TIDAK ADA') . "<br>";
} else {
    echo "\$appConfig TIDAK DI-SET atau bukan array.<br>";
}
echo "<hr>";

echo "<strong>Isi \$filterValues (Filter yang diterapkan):</strong><br>";
if (isset($filterValues)) {
    echo "<pre>"; print_r($filterValues); echo "</pre>";
} else {
    echo "\$filterValues TIDAK DI-SET oleh Controller.<br>";
}
echo "<hr>";

echo "<strong>Isi \$daftarKos (Data Kos yang akan ditampilkan):</strong><br>";
if (isset($daftarKos)) {
    echo "Tipe data \$daftarKos: " . gettype($daftarKos) . "<br>";
    if (is_array($daftarKos)) {
        echo "Jumlah item di \$daftarKos: " . count($daftarKos) . "<br>";
        if (count($daftarKos) > 0 && count($daftarKos) <= 5) { // Tampilkan beberapa item pertama jika tidak terlalu banyak
            echo "Beberapa item pertama di \$daftarKos: <pre>"; print_r(array_slice($daftarKos, 0, 5)); echo "</pre>";
        } elseif(count($daftarKos) > 5) {
            echo "Menampilkan 5 dari " . count($daftarKos) . " item pertama: <pre>"; print_r(array_slice($daftarKos, 0, 5)); echo "</pre>";
        }
    } else {
        echo "\$daftarKos bukan array.<br>";
    }
} else {
    echo "\$daftarKos TIDAK DI-SET oleh Controller.<br>";
}
echo "<hr>";

echo "<strong>Isi \$pagination (Data untuk Pagination):</strong><br>";
if (isset($pagination)) {
    echo "<pre>"; print_r($pagination); echo "</pre>";
} else {
    echo "\$pagination TIDAK DI-SET oleh Controller.<br>";
}
echo "</div>";
// ---- AKHIR BLOK DEBUG ----

$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7'; 
$paletteMediumBlue = '#4A90E2'; 
$paletteDarkBlue = '#1A3A5B';

$currentSearchTerm = $filterValues['search_term'] ?? '';
$currentKategori = $filterValues['kategori'] ?? '';
$currentMinHarga = $filterValues['min_harga'] ?? '';
$currentMaxHarga = $filterValues['max_harga'] ?? '';
$currentFilterStatus = $filterValues['status'] ?? '';
$currentFasilitas = $filterValues['fasilitas'] ?? '';

$currentPage = $pagination['currentPage'] ?? 1;
$totalPages = $pagination['totalPages'] ?? 1;
$paginationBaseUrl = $pagination['baseUrl'] ?? ($appConfig['BASE_URL'] . 'kos/daftar/'); 
$filterQueryString = $pagination['queryString'] ?? '';

?>
<style>
    .kos-item img { transition: transform 0.3s ease-in-out; }
    .kos-item:hover img { transform: scale(1.05); }
    .filter-modal-body { max-height: 70vh; overflow-y: auto; }
    .pagination .page-item.active .page-link { z-index: 3; color: #fff; background-color: #007bff; border-color: #007bff;}
    .btn-custom-filter { background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; color: <?php echo htmlspecialchars($paletteWhite); ?>; }
    .btn-custom-filter:hover { background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; color: <?php echo htmlspecialchars($paletteWhite); ?>;}
</style>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Daftar Kos Tersedia'); ?></h2>
</div>

<!-- Tombol untuk memicu Modal Filter -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <button type="button" class="btn btn-custom-filter" data-bs-toggle="modal" data-bs-target="#filterKosModal">
        <i class="fas fa-filter me-1"></i> Filter & Cari Kos
    </button>
    <?php if (!empty($filterQueryString) && $filterQueryString !== '?'):  ?>
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i> Reset Filter
        </a>
    <?php endif; ?>
</div>


<!-- Modal Filter -->
<div class="modal fade" id="filterKosModal" tabindex="-1" aria-labelledby="filterKosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterKosModalLabel"><i class="fas fa-sliders-h me-2"></i>Filter & Pencarian Kos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" method="GET">
                <div class="modal-body filter-modal-body">
                    <div class="mb-3">
                        <label for="search_term_modal" class="form-label">Cari Nama atau Alamat Kos</label>
                        <input type="text" class="form-control" id="search_term_modal" name="search_term" value="<?php echo htmlspecialchars($currentSearchTerm); ?>" placeholder="Masukkan nama atau area kos...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filter_kategori_modal" class="form-label">Kategori Kos</label>
                            <select class="form-select" id="filter_kategori_modal" name="kategori">
                                <option value="">Semua Kategori</option>
                                <option value="putra" <?php echo ($currentKategori === 'putra') ? 'selected' : ''; ?>>Putra</option>
                                <option value="putri" <?php echo ($currentKategori === 'putri') ? 'selected' : ''; ?>>Putri</option>
                                <option value="campur" <?php echo ($currentKategori === 'campur') ? 'selected' : ''; ?>>Campur</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filter_status_modal" class="form-label">Ketersediaan</label>
                            <select class="form-select" id="filter_status_modal" name="status">
                                <option value="">Semua Status</option>
                                <option value="available" <?php echo ($currentFilterStatus === 'available') ? 'selected' : ''; ?>>Tersedia (Available)</option>
                                <option value="booked" <?php echo ($currentFilterStatus === 'booked') ? 'selected' : ''; ?>>Sudah Dipesan (Booked)</option>
                                <!-- <option value="maintenance" <?php // echo ($currentFilterStatus === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option> -->
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="min_harga_modal" class="form-label">Harga Minimum (Rp)</label>
                            <input type="number" class="form-control" id="min_harga_modal" name="min_harga" value="<?php echo htmlspecialchars($currentMinHarga); ?>" placeholder="Contoh: 500000" step="50000">
                        </div>
                        <div class="col-md-6">
                            <label for="max_harga_modal" class="form-label">Harga Maksimum (Rp)</label>
                            <input type="number" class="form-control" id="max_harga_modal" name="max_harga" value="<?php echo htmlspecialchars($currentMaxHarga); ?>" placeholder="Contoh: 2000000" step="50000">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="filter_fasilitas_modal" class="form-label">Fasilitas (pisahkan dengan koma)</label>
                        <input type="text" class="form-control" id="filter_fasilitas_modal" name="fasilitas" value="<?php echo htmlspecialchars($currentFasilitas); ?>" placeholder="Contoh: AC, WiFi, Kamar Mandi Dalam">
                        <small class="form-text text-muted">Cari kos yang memiliki salah satu atau semua fasilitas yang disebutkan (tergantung implementasi backend).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" class="btn btn-outline-secondary">Reset Semua Filter</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if (isset($appConfig) && isset($daftarKos)): ?>
    <?php if (!empty($daftarKos)): ?>
        <div class="kos-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 20px;">
            <?php foreach ($daftarKos as $item_kos): ?>
                <div class="kos-item" style="border: 1px solid #e0e0e0; padding: 0; border-radius: 8px; background-color: #ffffff; box-shadow: 0 4px 8px rgba(0,0,0,0.05); transition: box-shadow 0.3s ease; overflow:hidden; display:flex; flex-direction:column;">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" style="text-decoration: none; color: inherit; display:block;">
                        <?php if (!empty($item_kos['gambar_utama'])): ?>
                            <img src="<?php echo htmlspecialchars($appConfig['UPLOADS_URL_PATH'] . $item_kos['gambar_utama']); ?>" 
                                 alt="Gambar <?php echo htmlspecialchars($item_kos['nama_kos']); ?>" 
                                 style="width: 100%; height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 180px; background-color: #f0f0f0; display:flex; align-items:center; justify-content:center; color:#aaa;">
                                <span>Tidak ada gambar</span>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div style="padding:15px; flex-grow:1; display:flex; flex-direction:column;">
                        <h3 style="margin-top:0; margin-bottom: 8px; font-size: 1.15em; color: #333; min-height: 44px; /* Untuk 2 baris judul */">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" style="text-decoration: none; color: #0056b3;">
                                <?php echo htmlspecialchars($item_kos['nama_kos']); ?>
                            </a>
                        </h3>
                        <p style="font-size: 0.85em; color: #555; margin-bottom: 8px; flex-grow:1;"><small><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($item_kos['alamat']); ?></small></p>
                        <p style="font-size: 1em; color: #007bff; font-weight: bold; margin-bottom: 8px;">Rp <?php echo number_format($item_kos['harga_per_bulan'], 0, ',', '.'); ?> <small>/ bulan</small></p>
                        <div style="font-size: 0.8em; color: #666; margin-bottom: 10px; display:flex; justify-content:space-between;">
                            <span><i class="fas fa-bed me-1"></i> <?php echo htmlspecialchars($item_kos['jumlah_kamar_tersedia'] ?? 0); ?> / <?php echo htmlspecialchars($item_kos['jumlah_kamar_total'] ?? 0); ?> kamar</span>
                            <span>
                                Status: 
                                <span style="font-weight: bold; padding: 2px 6px; border-radius: 4px; color: white; background-color: <?php 
                                    $statusValue = $item_kos['status_kos'] ?? 'maintenance'; 
                                    $bgColor = '#6c757d'; $textColor = 'white';
                                    switch ($statusValue) {
                                        case 'available': $bgColor = '#28a745'; break; 
                                        case 'booked': $bgColor = '#dc3545'; break;    
                                        case 'maintenance': $bgColor = '#ffc107'; $textColor = '#212529'; break; 
                                    } echo $bgColor;
                                ?>; color: <?php echo $textColor; ?>;">
                                    <?php echo ucfirst(htmlspecialchars($item_kos['status_kos'] ?? 'N/A')); ?>
                                </span>
                            </span>
                        </div>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" 
                           style="display: block; width:100%; text-align:center; padding: 10px 0px; background-color: #007bff; color: white; text-decoration: none; border-radius: 0 0 8px 8px; font-size: 0.9em; transition: background-color 0.2s; margin-top:auto;">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . 'page/' . ($currentPage - 1) . $filterQueryString); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . 'page/' . $i . $filterQueryString); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlspecialchars($paginationBaseUrl . 'page/' . ($currentPage + 1) . $filterQueryString); ?>" aria-label="Next">
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
