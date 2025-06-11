<?php
// File: views/admin/users/list.php
// Assumes $pageTitle and $daftarPengguna are passed to this view.
// Assumes $appConfig is available from layout_admin.php.
// (adjustBrightness() function is no longer needed here as Bootstrap handles striping)

?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Manajemen Pengguna'); ?></h2>
</div>

<?php if (empty($daftarPengguna)): ?>
    <div class="alert alert-info" role="alert">
        Belum ada data pengguna yang terdaftar.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr class="table-header-custom">
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th class="text-center">Admin?</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftarPengguna as $pengguna): ?>
                    <tr>
                        <td data-label="ID"><?php echo htmlspecialchars($pengguna['id']); ?></td>
                        <td data-label="Nama"><?php echo htmlspecialchars($pengguna['nama']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($pengguna['email']); ?></td>
                        <td data-label="No. Telepon"><?php echo htmlspecialchars($pengguna['no_telepon'] ?? '-'); ?></td>
                        <td data-label="Admin?" class="text-center">
                            <?php
                                $isAdminText = $pengguna['is_admin'] ? 'Ya' : 'Tidak';
                                $isAdminClass = $pengguna['is_admin'] ? 'bg-success' : 'bg-secondary'; // Bootstrap classes for badges
                            ?>
                            <span class="badge <?php echo $isAdminClass; ?>"><?php echo $isAdminText; ?></span>
                        </td>
                        <td data-label="Aksi" class="text-center">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/userEdit/' . $pengguna['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                            <?php /*
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/userDelete/' . $pengguna['id']); ?>"
                                onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');"
                                class="btn btn-sm btn-danger">Hapus</a>
                            */ ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>