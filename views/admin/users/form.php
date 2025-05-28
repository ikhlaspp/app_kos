<?php
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#DCE6F5'; // A light, muted blue
$paletteMediumBlue = '#4682B4'; // A steel blue/medium blue
$paletteDarkBlue = '#192846'; // A very dark blue/navy

$pageTitle = $pageTitle ?? 'Kelola Data Pengguna';
?>

<div style="
    font-family: 'Inter', sans-serif;
    color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    padding: 20px;
    background-color: <?php echo htmlspecialchars($paletteWhite); ?>; /* Main background */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 600px; /* Adjusted for form width */
    margin: 20px auto;
">
    <h2 style="
        text-align: center;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        margin-bottom: 30px;
    "><?php echo htmlspecialchars($pageTitle); ?></h2>

    <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST">
        <div style="margin-bottom: 15px;">
            <label for="nama" style="display:block; margin-bottom:5px; font-weight: 600;">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" required style="
                width:100%;
                padding:10px;
                border:1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;
                border-radius:6px; /* Slightly more rounded */
                box-sizing: border-box; /* Include padding in width */
                font-size: 1rem;
                color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
            ">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="email_display" style="display:block; margin-bottom:5px; font-weight: 600;">Email (tidak dapat diubah oleh admin di sini):</label>
            <input type="email" id="email_display" name="email_display" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled style="
                width:100%;
                padding:10px;
                border:1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;
                border-radius:6px;
                box-sizing: border-box;
                background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>; /* Use light blue for disabled */
                color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
                font-size: 1rem;
                cursor: not-allowed;
            ">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="no_telepon" style="display:block; margin-bottom:5px; font-weight: 600;">Nomor Telepon:</label>
            <input type="tel" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>" style="
                width:100%;
                padding:10px;
                border:1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;
                border-radius:6px;
                box-sizing: border-box;
                font-size: 1rem;
                color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
            ">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="alamat" style="display:block; margin-bottom:5px; font-weight: 600;">Alamat:</label>
            <textarea id="alamat" name="alamat" rows="3" style="
                width:100%;
                padding:10px;
                border:1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;
                border-radius:6px;
                box-sizing: border-box;
                resize: vertical; /* Allow vertical resizing */
                font-size: 1rem;
                color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
            "><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
        </div>
        <div style="margin-bottom: 20px; display: flex; align-items: center;">
            <input type="hidden" name="is_admin" value="0"> <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo !empty($user['is_admin']) ? 'checked' : ''; ?> style="
                margin-right: 8px; /* Space between checkbox and label */
                transform: scale(1.2); /* Slightly larger checkbox */
            ">
            <label for="is_admin" style="font-weight: 600;">Jadikan sebagai Admin</label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="submit" style="
                padding:12px 25px;
                background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>; /* Medium blue for submit */
                color:<?php echo htmlspecialchars($paletteWhite); ?>;
                border:none;
                border-radius:8px;
                cursor:pointer;
                font-weight: bold;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='<?php echo htmlspecialchars($paletteMediumBlue); ?>'; this.style.transform='translateY(0)';">
                Simpan Perubahan
            </button>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="
                display: inline-block; /* Make link behave like a block for padding */
                padding:12px 25px;
                margin-left:10px;
                text-decoration:none;
                color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; /* Dark blue for cancel link */
                background-color: transparent;
                border: 1px solid <?php echo htmlspecialchars($paletteLightBlue); ?>;
                border-radius:8px;
                font-weight: bold;
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteLightBlue); ?>'; this.style.color='<?php echo htmlspecialchars($paletteDarkBlue); ?>';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='<?php echo htmlspecialchars($paletteDarkBlue); ?>';">
                Batal
            </a>
        </div>
    </form>
</div>
