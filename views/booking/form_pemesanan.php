<?php
// views/booking/form_pemesanan.php
// Variables $pageTitle, $appConfig, $kos, and $input (if there's a POST error) are available.
// New: $usableVouchers is passed from BookingController->pesan()

$harga_kos_rp = number_format($kos['harga_per_bulan'], 0, ',', '.');

// Retrieve old input values if there was a submission error, otherwise pre-fill
$input_nama_pembayar = htmlspecialchars($input['nama_pembayar'] ?? $_SESSION['user_nama'] ?? '');
$input_kontak_pembayar = htmlspecialchars($input['kontak_pembayar'] ?? $_SESSION['user_no_telepon'] ?? ''); // Pre-fill with user's phone if available
$input_tanggal_mulai = htmlspecialchars($input['tanggal_mulai'] ?? date('Y-m-d'));
$input_durasi_sewa = htmlspecialchars($input['durasi_sewa'] ?? '1');
$input_metode_pembayaran = htmlspecialchars($input['metode_pembayaran'] ?? '');
// FIX: Changed to input_voucher_id to reflect it's the voucher's ID
$input_voucher_id = htmlspecialchars($input['voucher_id'] ?? ''); 

// Custom Color Palette for consistency
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';
$paletteMediumBlue = '#4A90E2';
$paletteDarkBlue = '#1A3A5B';
$paletteTextPrimary = '#0D2A57'; // Main text color (intended for form fields)
$paletteTextSecondary = '#555555'; // Secondary text color

// Define theme colors for buttons and highlights
$btnPrimaryBg = $paletteMediumBlue;
$btnPrimaryHoverBg = $paletteDarkBlue;
$btnSuccessBg = '#28a745';
$btnSuccessHoverBg = '#218838';

// Status color for info/alert boxes
$infoBg = '#d1ecf1';
$infoBorder = '#bee5eb';
$infoText = '#0c5460';
$warningBg = '#fff3cd';
$warningBorder = '#ffeeba';
$warningText = '#856404';
?>

<style>
    /* General styles for form elements */
    .form-control, .form-select, .input-group-text {
        border-radius: 0.25rem;
    }
    .form-label {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    /* FIX: Ensure dropdown (select) text color is black/primary text color */
    .form-select {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }
    /* This rule ensures placeholder/unselected options are also dark */
    .form-select option {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }


    /* Section headers */
    h4 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }

    /* Order Summary and Price Boxes */
    .summary-box {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .order-summary {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
    }
    .price-details-box {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
    }
    .total-price-box {
        background-color: <?php echo htmlspecialchars($warningBg); ?>;
        border: 1px solid <?php echo htmlspecialchars($warningBorder); ?>;
        color: <?php echo htmlspecialchars($warningText); ?>;
    }
    .total-price-box strong {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-size: 1.3em;
    }
    .discount-info {
        font-weight: bold;
        color: <?php echo htmlspecialchars($btnSuccessBg); ?>;
    }
    .discount-info span {
        font-weight: normal;
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }


    /* Buttons */
    .btn-primary {
        background-color: <?php echo htmlspecialchars($btnPrimaryBg); ?>;
        border-color: <?php echo htmlspecialchars($btnPrimaryBg); ?>;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: <?php echo htmlspecialchars($btnPrimaryHoverBg); ?>;
        border-color: <?php echo htmlspecialchars($btnPrimaryHoverBg); ?>;
    }
    .btn-success {
        background-color: <?php echo htmlspecialchars($btnSuccessBg); ?>;
        border-color: <?php echo htmlspecialchars($btnSuccessBg); ?>;
        font-weight: 600;
    }
    .btn-success:hover {
        background-color: <?php echo htmlspecialchars($btnSuccessHoverBg); ?>;
        border-color: <?php echo htmlspecialchars($btnSuccessHoverBg); ?>;
    }

    /* Hide the voucher feedback when not needed */
    #voucher-feedback.valid { color: #28a745; }
    #voucher-feedback.invalid { color: #dc3545; }

    <?php
    function adjustBrightness($hex, $steps) {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        $rgb = [];
        if (strlen($hex) == 3) {
            $rgb[0] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb[1] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb[2] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $rgb[0] = hexdec(substr($hex, 0, 2));
            $rgb[1] = hexdec(substr($hex, 2, 2));
            $rgb[2] = hexdec(substr($hex, 4, 2));
        }
        $rgb[0] = max(0, min(255, $rgb[0] + $steps));
        $rgb[1] = max(0, min(255, $rgb[1] + $steps));
        $rgb[2] = max(0, min(255, $rgb[2] + $steps));
        return '#' . str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);
    }
    ?>
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <h2 class="mb-4 text-center"><?php echo htmlspecialchars($pageTitle ?? 'Form Pemesanan & Pembayaran'); ?></h2>

            <div class="summary-box order-summary">
                <h4>Detail Kos yang Dipesan:</h4>
                <p class="mb-1"><strong>Nama Kos:</strong> <?php echo htmlspecialchars($kos['nama_kos']); ?></p>
                <p class="mb-1"><strong>Harga per Bulan:</strong> Rp <?php echo $harga_kos_rp; ?></p>
                <p class="mb-0"><strong>Biaya Admin:</strong> 10%</p>
            </div>

            <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'booking/pesan/' . $kos['id']); ?>" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="kos_id" value="<?php echo htmlspecialchars($kos['id']); ?>">
                <input type="hidden" name="harga_per_bulan_raw" id="harga-per-bulan-raw" value="<?php echo htmlspecialchars($kos['harga_per_bulan']); ?>">

                <h4>Detail Pemesanan:</h4>
                <div class="mb-3">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai Sewa:</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" value="<?php echo $input_tanggal_mulai; ?>" required min="<?php echo date('Y-m-d'); ?>">
                    <div class="invalid-feedback">Tanggal mulai sewa wajib diisi dan tidak boleh di masa lalu.</div>
                </div>
                <div class="mb-3">
                    <label for="durasi_sewa" class="form-label">Durasi Sewa (bulan):</label>
                    <select id="durasi_sewa" name="durasi_sewa" class="form-select" required>
                        <option value="1" <?php echo ($input_durasi_sewa == '1') ? 'selected' : ''; ?>>1 Bulan</option>
                        <option value="3" <?php echo ($input_durasi_sewa == '3') ? 'selected' : ''; ?>>3 Bulan</option>
                        <option value="6" <?php echo ($input_durasi_sewa == '6') ? 'selected' : ''; ?>>6 Bulan</option>
                        <option value="12" <?php echo ($input_durasi_sewa == '12') ? 'selected' : ''; ?>>12 Bulan (1 Tahun)</option>
                    </select>
                    <div class="invalid-feedback">Durasi sewa wajib dipilih.</div>
                </div>
                <p class="text-muted fst-italic mb-3">Total harga akan dihitung berdasarkan durasi sewa.</p>

                <h4 class="mt-4">Informasi Pembayar:</h4>
                <div class="mb-3">
                    <label for="nama_pembayar" class="form-label">Nama Lengkap Sesuai Identitas:</label>
                    <input type="text" id="nama_pembayar" name="nama_pembayar" class="form-control" value="<?php echo $input_nama_pembayar; ?>" required>
                    <div class="invalid-feedback">Nama pembayar wajib diisi.</div>
                </div>
                <div class="mb-3">
                    <label for="kontak_pembayar" class="form-label">No. Telepon / Email Aktif:</label>
                    <input type="text" id="kontak_pembayar" name="kontak_pembayar" class="form-control" value="<?php echo $input_kontak_pembayar; ?>" required>
                    <div class="invalid-feedback">Kontak pembayar wajib diisi.</div>
                </div>

                <h4 class="mt-4">Detail Pembayaran:</h4>

                <div class="mb-3">
                    <label for="voucher_id" class="form-label">Gunakan Voucher (Opsional):</label>
                    <select id="voucher_id" name="voucher_id" class="form-select">
                        <option value="">-- Pilih Voucher --</option>
                        <?php if (!empty($usableVouchers)): ?>
                            <?php foreach ($usableVouchers as $voucher): ?>
                                <option 
                                    value="<?php echo htmlspecialchars($voucher['voucher_id']); ?>" data-code="<?php echo htmlspecialchars($voucher['code']); ?>"
                                    data-type="<?php echo htmlspecialchars($voucher['type']); ?>"
                                    data-value="<?php echo htmlspecialchars($voucher['value']); ?>"
                                    data-min-transaction="<?php echo htmlspecialchars($voucher['min_transaction_amount'] ?? 0); ?>"
                                    data-max-discount="<?php echo htmlspecialchars($voucher['max_discount_amount'] ?? 'Infinity'); ?>"
                                    <?php echo ($input_voucher_id == $voucher['id']) ? 'selected' : ''; ?>
                                >
                                    <?php
                                        // Display voucher code and name prominently
                                        echo htmlspecialchars($voucher['code'] . ' - ' . $voucher['name']);
                                        // Add details in parentheses
                                        echo " (";
                                        echo $voucher['type'] === 'percentage' ? htmlspecialchars($voucher['value']) . '%' : 'Rp ' . number_format($voucher['value'], 0, ',', '.');
                                        if ($voucher['min_transaction_amount']) {
                                            echo " - Min. Rp " . number_format($voucher['min_transaction_amount'], 0, ',', '.');
                                        }
                                        echo " - Exp: " . date('d/m/Y', strtotime($voucher['expiration_date']));
                                        echo ")";
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small id="voucher-feedback" class="form-text mt-2 text-muted">Pilih voucher yang sudah Anda klaim di dashboard.</small>
                </div>

                <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran:</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank BCA" <?php echo ($input_metode_pembayaran === 'Transfer Bank BCA') ? 'selected' : ''; ?>>Transfer Bank BCA</option>
                        <option value="Transfer Bank Mandiri" <?php echo ($input_metode_pembayaran === 'Transfer Bank Mandiri') ? 'selected' : ''; ?>>Transfer Bank Mandiri</option>
                        <option value="GoPay" <?php echo ($input_metode_pembayaran === 'GoPay') ? 'selected' : ''; ?>>GoPay</option>
                        <option value="OVO" <?php echo ($input_metode_pembayaran === 'OVO') ? 'selected' : ''; ?>>OVO</option>
                    </select>
                    <div class="invalid-feedback">Metode pembayaran wajib dipilih.</div>
                </div>

                <div class="summary-box price-details-box mt-4">
                    <p class="mb-1"><strong>Rincian Harga:</strong></p>
                    <div class="mt-2">
                        <p class="mb-1 d-flex justify-content-between">
                            <span>Harga Awal (<span id="durasi-display">1</span> bulan):</span>
                            <span>Rp <span id="harga-awal"><?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?></span></span>
                        </p>
                        <p class="mb-1 d-flex justify-content-between">
                            <span>Pajak (10%):</span>
                            <span>Rp <span id="pajak"><?php echo number_format($kos['harga_per_bulan'] * 0.10, 0, ',', '.'); ?></span></span>
                        </p>
                        <hr class="my-2">
                        <p class="mb-1 d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>Rp <span id="subtotal"><?php echo number_format($kos['harga_per_bulan'] * 1.10, 0, ',', '.'); ?></span></span>
                        </p>
                        <p class="mb-0 d-flex justify-content-between discount-info" id="discount-row" style="display: none;">
                            <span>Diskon Voucher (<span id="voucher-display-text"></span>):</span>
                            <span class="text-danger">- Rp <span id="diskon-jumlah">0</span></span>
                        </p>
                    </div>
                </div>

                <div class="summary-box total-price-box">
                    <p class="mb-0 d-flex justify-content-between">
                        <strong>Total Pembayaran:</strong>
                        <strong>Rp <span id="total"><?php echo number_format($kos['harga_per_bulan'] * 1.10, 0, ',', '.'); ?></span></strong>
                    </p>
                </div>

                <button type="submit" class="btn btn-success w-100 mt-4">Pesan dan Bayar</button>
            </form>

            <p class="mt-4 text-center">
                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $kos['id']); ?>">Batal dan Kembali ke Detail Kos</a>
            </p>
        </div>
    </div>
</div>

<script>
    const hargaPerBulan = <?php echo json_encode($kos['harga_per_bulan']); ?>;
    const durasiSelect = document.getElementById('durasi_sewa');
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    const durasiDisplay = document.getElementById('durasi-display');
    const hargaAwalEl = document.getElementById('harga-awal');
    const pajakEl = document.getElementById('pajak');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const voucherIdSelect = document.getElementById('voucher_id');
    const voucherFeedbackEl = document.getElementById('voucher-feedback');
    const discountRowEl = document.getElementById('discount-row');
    const diskonJumlahEl = document.getElementById('diskon-jumlah');
    const voucherDisplayText = document.getElementById('voucher-display-text');

    let selectedVoucher = null;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function calculateTotalPrice() {
        const durasi = parseInt(durasiSelect.value);
        const hargaAwal = hargaPerBulan * durasi;
        const pajak = hargaAwal * 0.10;
        let subtotal = hargaAwal + pajak;
        let finalTotal = subtotal;

        let discountAmount = 0;

        if (voucherIdSelect.value !== '') { // Check if a voucher is selected (not the default empty option)
            const selectedOption = voucherIdSelect.options[voucherIdSelect.selectedIndex];
            selectedVoucher = {
                id: voucherIdSelect.value,
                code: selectedOption.dataset.code,
                type: selectedOption.dataset.type,
                value: parseFloat(selectedOption.dataset.value),
                min_transaction: parseFloat(selectedOption.dataset.minTransaction),
                max_discount: parseFloat(selectedOption.dataset.maxDiscount || Infinity)
            };

            if (subtotal >= selectedVoucher.min_transaction) {
                if (selectedVoucher.type === 'percentage') {
                    discountAmount = subtotal * (selectedVoucher.value / 100);
                    if (discountAmount > selectedVoucher.max_discount) {
                        discountAmount = selectedVoucher.max_discount;
                    }
                } else if (selectedVoucher.type === 'fixed_amount') {
                    discountAmount = selectedVoucher.value;
                }
                
                discountAmount = Math.min(discountAmount, subtotal);
                finalTotal = subtotal - discountAmount;

                diskonJumlahEl.textContent = formatRupiah(discountAmount);
                voucherDisplayText.textContent = selectedVoucher.code + ' (' + (selectedVoucher.type === 'percentage' ? selectedVoucher.value + '%' : 'Rp ' + formatRupiah(selectedVoucher.value)) + ')';
                discountRowEl.style.display = 'flex';
                voucherFeedbackEl.textContent = 'Voucher berhasil diterapkan!';
                voucherFeedbackEl.className = 'form-text mt-2 text-success';

            } else {
                discountAmount = 0;
                discountRowEl.style.display = 'none';
                voucherFeedbackEl.textContent = 'Voucher memerlukan minimal transaksi Rp ' + formatRupiah(selectedVoucher.min_transaction) + '.';
                voucherFeedbackEl.className = 'form-text mt-2 text-warning';
            }
        } else {
            selectedVoucher = null;
            discountAmount = 0;
            discountRowEl.style.display = 'none';
            voucherFeedbackEl.textContent = 'Pilih voucher yang sudah Anda klaim di dashboard.';
            voucherFeedbackEl.className = 'form-text mt-2 text-muted';
        }

        hargaAwalEl.textContent = formatRupiah(hargaAwal);
        pajakEl.textContent = formatRupiah(pajak);
        subtotalEl.textContent = formatRupiah(subtotal);
        durasiDisplay.textContent = durasi;
        totalEl.textContent = formatRupiah(Math.max(0, finalTotal));
    }

    voucherIdSelect.addEventListener('change', calculateTotalPrice);
    durasiSelect.addEventListener('change', calculateTotalPrice);
    tanggalMulaiInput.addEventListener('change', calculateTotalPrice);

    document.addEventListener('DOMContentLoaded', function() {
        const userContact = <?php echo json_encode($_SESSION['user_no_telepon'] ?? ''); ?>;
        if (userContact && !document.getElementById('kontak_pembayar').value) {
            document.getElementById('kontak_pembayar').value = userContact;
        }
        
        if (voucherIdSelect.value !== '') {
            const selectedOption = voucherIdSelect.options[voucherIdSelect.selectedIndex];
            selectedVoucher = {
                id: voucherIdSelect.value,
                code: selectedOption.dataset.code,
                type: selectedOption.dataset.type,
                value: parseFloat(selectedOption.dataset.value),
                min_transaction: parseFloat(selectedOption.dataset.minTransaction),
                max_discount: parseFloat(selectedOption.dataset.maxDiscount || Infinity)
            };
        }
        calculateTotalPrice();
    });

    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
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