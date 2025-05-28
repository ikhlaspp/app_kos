<?php
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#DCE6F5'; // A light, muted blue
$paletteMediumBlue = '#4682B4'; // A steel blue/medium blue
$paletteDarkBlue = '#192846'; // A very dark blue/navy

$pageTitle = $pageTitle ?? 'Kelola Data Kos';
?>

<div style="
    font-family: 'Inter', sans-serif;
    color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    padding: 20px;
    background-color: <?php echo htmlspecialchars($paletteWhite); ?>; /* Main background */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 1200px;
    margin: 20px auto;
">
    <h2 style="
        text-align: center;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        margin-bottom: 30px;
    "><?php echo htmlspecialchars($pageTitle); ?></h2>

    <p style="margin-bottom: 25px; text-align: right;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosCreate'); ?>" style="
            display: inline-block;
            padding: 12px 25px;
            background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; /* Medium blue for add button */
            color: <?php echo htmlspecialchars($paletteWhite); ?>;
            text-decoration: none;
            border-radius: 8px; /* More rounded corners */
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='<?php echo htmlspecialchars($paletteMediumBlue); ?>'; this.style.transform='translateY(0)';">
            + Tambah Kos Baru
        </a>
    </p>

    <?php if (!empty($daftarKos)): ?>
        <div style="overflow-x: auto;">
            <table style="
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
                border-radius: 8px;
                overflow: hidden; /* Ensures rounded corners apply to table */
            ">
                <thead>
                    <tr style="
                        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; /* Light blue for table header */
                        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
                    ">
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
                    <tr style="background-color: <?php echo ($index % 2 == 0) ? $paletteWhite : '#F8F8F8'; ?>;"> <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars($kos['id']); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars($kos['nama_kos']); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;">Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;"><?php echo htmlspecialchars(ucfirst($kos['status_kos'])); ?></td>
                        <td style="padding: 10px 12px; border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>; text-align:center;">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id']); ?>" style="
                                text-decoration:none;
                                color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; /* Medium blue for edit link */
                                margin-right:10px;
                                font-weight: 600;
                                transition: color 0.3s ease;
                            " onmouseover="this.style.color='<?php echo htmlspecialchars($paletteDarkBlue); ?>';" onmouseout="this.style.color='<?php echo htmlspecialchars($paletteMediumBlue); ?>';">Edit</a>
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']); ?>"
                               style="
                                text-decoration:none;
                                color: #DC3545; /* Standard red for delete */
                                font-weight: 600;
                                transition: color 0.3s ease;
                            " onmouseover="this.style.color='#C82333';" onmouseout="this.style.color='#DC3545';">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align: center; padding: 20px; font-style: italic;">Belum ada data kos.</p>
    <?php endif; ?>
</div>
