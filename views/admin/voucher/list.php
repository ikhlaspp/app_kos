<?php
// File: views/admin/voucher/list.php
// Assumes $vouchers array is passed from AdminController->manageVouchers()
// Assumes $appConfig is available from layout_admin.php.

// Variables passed from AdminController
$pageTitle = $pageTitle ?? 'Kelola Data Voucher';

// Ensure $vouchers is defined for display purposes, even if empty
if (!isset($vouchers)) {
    $vouchers = []; // Default to empty array to avoid PHP errors if not passed
}

// Pagination data (as added in previous step)
$paginationBaseUrl = $pagination['baseUrl'] ?? ($appConfig['BASE_URL'] . 'admin/voucher');
$currentPage = $pagination['currentPage'] ?? 1;
$totalPages = $pagination['totalPages'] ?? 1;
$filterQueryString = $pagination['queryString'] ?? '';

// For link pagination: ensure query string filter is added after ?page=X
$pageQueryPrefix = !empty($filterQueryString) ? '&' : '?';
if (empty($filterQueryString) && strpos($paginationBaseUrl, '?') !== false) {
    $pageQueryPrefix = '&';
} elseif (empty($filterQueryString)) {
    $pageQueryPrefix = '?';
}

?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
</div>

<div class="mb-3 d-flex justify-content-end">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/createVoucher'); ?>" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Buat Voucher Baru
    </a>
</div>

<?php if (empty($vouchers)): ?>
    <div class="alert alert-info" role="alert">
        Belum ada voucher yang terdaftar.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr class="table-header-custom">
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    <th>Min. Transaksi</th>
                    <th>Max. Diskon</th>
                    <th class="text-center">Limit/Pengguna</th>
                    <th class="text-center">Total Limit</th>
                    <th class="text-center">Digunakan</th>
                    <th>Kadaluarsa</th>
                    <th class="text-center">Aktif</th>
                    <th class="text-center">Untuk Pengguna Baru</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vouchers as $index => $voucher): ?>
                    <tr>
                        <td data-label="ID"><?php echo htmlspecialchars($voucher['id']); ?></td>
                        <td data-label="Kode"><strong><?php echo htmlspecialchars($voucher['code']); ?></strong></td>
                        <td data-label="Nama"><?php echo htmlspecialchars($voucher['name']); ?></td>
                        <td data-label="Tipe">
                            <?php
                                $voucherTypeClass = '';
                                switch ($voucher['type']) {
                                    case 'percentage': $voucherTypeClass = 'bg-primary'; break;
                                    case 'fixed_amount': $voucherTypeClass = 'bg-dark'; break;
                                    default: $voucherTypeClass = 'bg-secondary'; break;
                                }
                            ?>
                            <span class="badge <?php echo $voucherTypeClass; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $voucher['type']))); ?></span>
                        </td>
                        <td data-label="Nilai">
                            <?php
                            if ($voucher['type'] === 'fixed_amount') {
                                echo 'Rp ' . number_format((float)$voucher['value'], 0, ',', '.'); 
                            } else { 
                                echo htmlspecialchars($voucher['value']) . '%';
                            }
                            ?>
                        </td>
                        <td data-label="Min. Transaksi"><?php echo $voucher['min_transaction_amount'] ? 'Rp ' . number_format($voucher['min_transaction_amount'], 0, ',', '.') : '-'; ?></td>
                        <td data-label="Max. Diskon"><?php echo $voucher['max_discount_amount'] ? 'Rp ' . number_format($voucher['max_discount_amount'], 0, ',', '.') : '-'; ?></td>
                        <td data-label="Limit/Pengguna" class="text-center"><?php echo htmlspecialchars($voucher['usage_limit_per_user']); ?></td>
                        <td data-label="Total Limit" class="text-center"><?php echo $voucher['total_usage_limit'] ? htmlspecialchars($voucher['total_usage_limit']) : 'Unlimited'; ?></td>
                        <td data-label="Digunakan" class="text-center"><?php echo htmlspecialchars($voucher['current_total_uses']); ?></td>
                        <td data-label="Kadaluarsa"><?php echo htmlspecialchars(date('d M Y', strtotime($voucher['expiration_date']))); ?></td>
                        <td data-label="Aktif?" class="text-center">
                            <?php
                                $isActiveText = $voucher['is_active'] ? 'Ya' : 'Tidak';
                                $isActiveClass = $voucher['is_active'] ? 'bg-success' : 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $isActiveClass; ?>"><?php echo $isActiveText; ?></span>
                        </td>
                        <td data-label="Untuk Pengguna Baru?" class="text-center">
                            <?php
                                $isNewUserText = $voucher['is_claimable_by_new_users'] ? 'Ya' : 'Tidak';
                                $isNewUserClass = $voucher['is_claimable_by_new_users'] ? 'bg-success' : 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $isNewUserClass; ?>"><?php echo $isNewUserText; ?></span>
                        </td>
                        <td data-label="Aksi" class="text-center">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/editVoucher/' . $voucher['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete-voucher-btn"
                                    data-bs-toggle="modal" data-bs-target="#deleteVoucherModal"
                                    data-bs-id="<?php echo htmlspecialchars($voucher['id']); ?>"
                                    data-bs-code="<?php echo htmlspecialchars($voucher['code']); ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

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

<p class="mt-3 text-center">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/dashboard'); ?>" class="btn btn-outline-secondary">Kembali ke Dashboard Admin</a>
</p>


<div class="modal fade" id="deleteVoucherModal" tabindex="-1" aria-labelledby="deleteVoucherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteVoucherModalLabel">Konfirmasi Hapus Voucher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteVoucherForm" method="POST" action="">
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus voucher <strong id="voucherCodeDisplay"></strong> (ID: <strong id="voucherIdDisplay"></strong>)?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Tindakan ini tidak dapat dibatalkan.</p>
                    <input type="hidden" name="voucher_id" id="deleteVoucherId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Voucher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?php echo htmlspecialchars($appConfig['BASE_URL']); ?>';

    // Delete Voucher Modal Logic
    const deleteVoucherModal = new bootstrap.Modal(document.getElementById('deleteVoucherModal'));
    const deleteVoucherButtons = document.querySelectorAll('.delete-voucher-btn'); // Select all delete buttons

    deleteVoucherButtons.forEach(button => {
        button.addEventListener('click', function() {
            const voucherId = this.dataset.bsId;
            const voucherCode = this.dataset.bsCode;
            
            // Populate modal fields
            document.getElementById('deleteVoucherId').value = voucherId;
            document.getElementById('voucherIdDisplay').textContent = voucherId;
            document.getElementById('voucherCodeDisplay').textContent = voucherCode;
            
            // Set the form action dynamically
            document.getElementById('deleteVoucherForm').action = `${baseUrl}admin/deleteVoucher/${voucherId}`;
            
            deleteVoucherModal.show(); // Show the modal
        });
    });
});
</script>