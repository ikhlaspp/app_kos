<?php

// Variabel $pageTitle, $appConfig, $kos, dan $input (jika ada error post) tersedia.

$harga_kos_rp = number_format($kos['harga_per_bulan'], 0, ',', '.');
// Ambil input lama jika ada (setelah submit ada error)
$input_nama_pembayar = htmlspecialchars($input['nama_pembayar'] ?? $_SESSION['user_nama'] ?? ''); // Pre-fill dengan nama user login
$input_kontak_pembayar = htmlspecialchars($input['kontak_pembayar'] ?? ''); // Bisa diisi dengan no_telp user jika ada
$input_tanggal_mulai = htmlspecialchars($input['tanggal_mulai'] ?? date('Y-m-d'));
$input_durasi_sewa = htmlspecialchars($input['durasi_sewa'] ?? '1');
$input_metode_pembayaran = htmlspecialchars($input['metode_pembayaran'] ?? '');
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Form Pemesanan & Pembayaran'); ?></h2>

<div class="order-summary" style="margin-bottom: 20px; padding:15px; border:1px solid #eee; background-color:#f9f9f9; border-radius:5px;">
    <h4>Detail Kos yang Dipesan:</h4>
    <p><strong>Nama Kos:</strong> <?php echo htmlspecialchars($kos['nama_kos']); ?></p>
    <p><strong>Harga per Bulan:</strong> Rp <?php echo $harga_kos_rp; ?></p>
</div>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'booking/pesan/' . $kos['id']); ?>" method="POST" style="max-width: 600px;">
    <h4>Detail Pemesanan:</h4>
    <div style="margin-bottom: 10px;">
        <label for="tanggal_mulai" style="display: block; margin-bottom: 5px;">Tanggal Mulai Sewa:</label>
        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo $input_tanggal_mulai; ?>" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="durasi_sewa" style="display: block; margin-bottom: 5px;">Durasi Sewa (bulan):</label>
        <select id="durasi_sewa" name="durasi_sewa" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="1" <?php echo ($input_durasi_sewa == '1') ? 'selected' : ''; ?>>1 Bulan</option>
            <option value="3" <?php echo ($input_durasi_sewa == '3') ? 'selected' : ''; ?>>3 Bulan</option>
            <option value="6" <?php echo ($input_durasi_sewa == '6') ? 'selected' : ''; ?>>6 Bulan</option>
            <option value="12" <?php echo ($input_durasi_sewa == '12') ? 'selected' : ''; ?>>12 Bulan (1 Tahun)</option>
        </select>
    </div>
    <p><em>Total harga akan dihitung berdasarkan durasi sewa.</em></p>

    <h4 style="margin-top:20px;">Informasi Pembayar:</h4>
    <div style="margin-bottom: 10px;">
        <label for="nama_pembayar" style="display: block; margin-bottom: 5px;">Nama Lengkap Sesuai Identitas:</label>
        <input type="text" id="nama_pembayar" name="nama_pembayar" value="<?php echo $input_nama_pembayar; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="kontak_pembayar" style="display: block; margin-bottom: 5px;">No. Telepon / Email Aktif:</label>
        <input type="text" id="kontak_pembayar" name="kontak_pembayar" value="<?php echo $input_kontak_pembayar; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>

    <h4 style="margin-top:20px;">Detail Pembayaran:</h4>
    <div style="margin-bottom: 10px;">
        <label for="metode_pembayaran" style="display: block; margin-bottom: 5px;">Metode Pembayaran:</label>
        <select id="metode_pembayaran" name="metode_pembayaran" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Metode --</option>
            <option value="Transfer Bank BCA" <?php echo ($input_metode_pembayaran === 'Transfer Bank BCA') ? 'selected' : ''; ?>>Transfer Bank BCA</option>
            <option value="Transfer Bank Mandiri" <?php echo ($input_metode_pembayaran === 'Transfer Bank Mandiri') ? 'selected' : ''; ?>>Transfer Bank Mandiri</option>
            <option value="GoPay" <?php echo ($input_metode_pembayaran === 'GoPay') ? 'selected' : ''; ?>>GoPay</option>
            <option value="OVO" <?php echo ($input_metode_pembayaran === 'OVO') ? 'selected' : ''; ?>>OVO</option>
        </select>
    </div>
    
    <button type="submit" style="padding: 12px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em;">Pesan dan Bayar</button>
</form>

<p style="margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $kos['id']); ?>">Batal dan Kembali ke Detail Kos</a>
</p>