<?php
// File: views/admin/voucher/form.php
// Assumes $pageTitle, $formAction, $voucher (for edit mode), $mode are passed.
// Assumes $appConfig is available from layout_admin.php.
// Assumes $errors and $oldInput are passed from the controller for validation feedback.

// Determine if in edit mode and set form action/pre-fill data
$isEdit = isset($mode) && $mode === 'edit' && isset($voucher) && $voucher;
$formAction = htmlspecialchars($formAction ?? ($appConfig['BASE_URL'] . 'admin/storeVoucher'));
$pageTitle = $pageTitle ?? ($isEdit ? "Edit Voucher" : "Buat Voucher Baru");

// Use $oldInput for repopulating form fields on validation errors
// Otherwise, use $voucher data for edit mode, or empty string for create mode
$input_code = htmlspecialchars($oldInput['code'] ?? ($voucher['code'] ?? ''));
$input_name = htmlspecialchars($oldInput['name'] ?? ($voucher['name'] ?? ''));
$input_description = htmlspecialchars($oldInput['description'] ?? ($voucher['description'] ?? ''));
$input_type = htmlspecialchars($oldInput['type'] ?? ($voucher['type'] ?? 'percentage')); // Default to percentage for new
$input_value = htmlspecialchars($oldInput['value'] ?? ($voucher['value'] ?? ''));
$input_min_transaction_amount = htmlspecialchars($oldInput['min_transaction_amount'] ?? ($voucher['min_transaction_amount'] ?? ''));
$input_max_discount_amount = htmlspecialchars($oldInput['max_discount_amount'] ?? ($voucher['max_discount_amount'] ?? ''));
$input_usage_limit_per_user = htmlspecialchars($oldInput['usage_limit_per_user'] ?? ($voucher['usage_limit_per_user'] ?? '1'));
$input_total_usage_limit = htmlspecialchars($oldInput['total_usage_limit'] ?? ($voucher['total_usage_limit'] ?? ''));

// Handle expiration_date for datetime-local input type
// If oldInput exists (validation error), use that. Otherwise, if editing, use voucher's date.
// For new vouchers, default to 1 month from now or current date.
if (isset($oldInput['expiration_date']) && !empty($oldInput['expiration_date'])) {
    // If it comes from $_POST as a date string, format it for datetime-local
    $input_expiration_date = date('Y-m-d\TH:i', strtotime($oldInput['expiration_date']));
} elseif ($isEdit && isset($voucher['expiration_date'])) {
    // If it's an existing voucher, format its expiration_date (which might be a DATE or DATETIME from DB)
    $input_expiration_date = date('Y-m-d\TH:i', strtotime($voucher['expiration_date']));
} else {
    // For new vouchers, default to approximately one month from now
    $input_expiration_date = date('Y-m-d\TH:i', strtotime('+1 month'));
}


// Checkbox states (use $oldInput first, then $voucher for edit, default to true for new active)
$input_is_active = (isset($oldInput['is_active']) && $oldInput['is_active'] == '1') || (!$isEdit && !isset($oldInput['is_active'])) || ($isEdit && $voucher['is_active']);
$input_is_claimable_by_new_users = (isset($oldInput['is_claimable_by_new_users']) && $oldInput['is_claimable_by_new_users'] == '1') || ($isEdit && $voucher['is_claimable_by_new_users']);

// Errors array (passed from controller on validation failure)
$errors = $errors ?? [];
?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
</div>

<div class="card p-4 mb-4"> <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Validasi Gagal!</h4>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo $formAction; ?>" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="code" class="form-label">Kode Voucher <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?php echo isset($errors['code']) ? 'is-invalid' : ''; ?>" id="code" name="code" value="<?php echo $input_code; ?>" required <?php echo $isEdit ? 'readonly' : ''; ?>>
            <?php if ($isEdit): ?>
                <small class="form-text text-muted">Kode voucher tidak bisa diubah setelah dibuat.</small>
            <?php endif; ?>
            <?php if (isset($errors['code'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['code']); ?></div><?php endif; ?>
            <div class="invalid-feedback">Kode Voucher wajib diisi.</div> </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nama Voucher <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $input_name; ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
            <div class="invalid-feedback">Nama Voucher wajib diisi.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $input_description; ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
                <select class="form-select <?php echo isset($errors['type']) ? 'is-invalid' : ''; ?>" id="type" name="type" required>
                    <option value="">Pilih Tipe</option>
                    <option value="percentage" <?php echo ($input_type === 'percentage') ? 'selected' : ''; ?>>Persentase (%)</option>
                    <option value="fixed_amount" <?php echo ($input_type === 'fixed_amount') ? 'selected' : ''; ?>>Jumlah Tetap (Rp)</option>
                </select>
                <?php if (isset($errors['type'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['type']); ?></div><?php endif; ?>
                <div class="invalid-feedback">Tipe Diskon wajib dipilih.</div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="value" class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                <input type="number" class="form-control <?php echo isset($errors['value']) ? 'is-invalid' : ''; ?>" id="value" name="value" step="0.01" min="0.01" value="<?php echo $input_value; ?>" required>
                <small class="form-text text-muted">Contoh: 10 untuk 10%, atau 50000 untuk Rp 50.000.</small>
                <?php if (isset($errors['value'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['value']); ?></div><?php endif; ?>
                <div class="invalid-feedback">Nilai Diskon harus lebih dari 0.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="min_transaction_amount" class="form-label">Min. Transaksi (Opsional)</label>
                <input type="number" class="form-control" id="min_transaction_amount" name="min_transaction_amount" step="1000" min="0" value="<?php echo $input_min_transaction_amount; ?>" placeholder="Min. transaksi agar voucher berlaku">
            </div>
            <div class="col-md-6 mb-3">
                <label for="max_discount_amount" class="form-label">Max. Diskon (Untuk Persentase, Opsional)</label>
                <input type="number" class="form-control" id="max_discount_amount" name="max_discount_amount" step="1000" min="0" value="<?php echo $input_max_discount_amount; ?>" placeholder="Diskon maksimal jika tipe persentase">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="usage_limit_per_user" class="form-label">Batas Penggunaan per Pengguna</label>
                <input type="number" class="form-control <?php echo isset($errors['usage_limit_per_user']) ? 'is-invalid' : ''; ?>" id="usage_limit_per_user" name="usage_limit_per_user" min="1" value="<?php echo $input_usage_limit_per_user; ?>" required>
                <small class="form-text text-muted">Berapa kali satu pengguna dapat mengklaim/menggunakan voucher ini.</small>
                <?php if (isset($errors['usage_limit_per_user'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['usage_limit_per_user']); ?></div><?php endif; ?>
                <div class="invalid-feedback">Batas penggunaan per pengguna tidak valid (minimal 1).</div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="total_usage_limit" class="form-label">Total Batas Penggunaan (Opsional)</label>
                <input type="number" class="form-control" id="total_usage_limit" name="total_usage_limit" min="1" value="<?php echo $input_total_usage_limit; ?>" placeholder="Biarkan kosong untuk tidak terbatas">
            </div>
        </div>

        <div class="mb-3">
            <label for="expiration_date" class="form-label">Tanggal & Waktu Kadaluarsa <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control <?php echo isset($errors['expiration_date']) ? 'is-invalid' : ''; ?>" id="expiration_date" name="expiration_date" value="<?php echo $input_expiration_date; ?>" required>
            <?php if (isset($errors['expiration_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['expiration_date']); ?></div><?php endif; ?>
            <div class="invalid-feedback">Tanggal Kadaluarsa wajib diisi dan harus format tanggal dan waktu yang valid.</div>
        </div>

        <div class="form-check mb-3 ps-4"> <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo $input_is_active ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active">
                Aktif
            </label>
        </div>

        <div class="form-check mb-3 ps-4"> <input class="form-check-input" type="checkbox" id="is_claimable_by_new_users" name="is_claimable_by_new_users" value="1" <?php echo $input_is_claimable_by_new_users ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_claimable_by_new_users">
                Diberikan ke Pengguna Baru (Otomatis)
            </label>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/voucher'); ?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Voucher' : 'Buat Voucher'; ?></button>
        </div>
    </form>
</div>

<script>
// Bootstrap form validation (standard copy)
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>