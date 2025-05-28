<?php
// This PHP file is a view for the admin dashboard.
// It expects the following variables to be available:
// - $pageTitle (string, optional): Title of the page.
// - $_SESSION['user_nama'] (string, optional): The name of the logged-in user.
// - $appConfig (array): Configuration array, expected to contain 'BASE_URL'.

// Define colors based on the provided palette for consistency.
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#DCE6F5'; // A light, muted blue
$paletteMediumBlue = '#4682B4'; // A steel blue/medium blue
$paletteDarkBlue = '#192846'; // A very dark blue/navy

// Ensure $pageTitle is set, default if not.
$pageTitle = $pageTitle ?? 'Admin Dashboard';
?>

<div style="
    font-family: 'Inter', sans-serif;
    color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    padding: 20px;
    background-color: <?php echo htmlspecialchars($paletteWhite); ?>; /* Main background */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px; /* Adjusted for dashboard width */
    margin: 20px auto;
    text-align: center; /* Center content within the dashboard box */
">
    <h2 style="
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        margin-bottom: 20px;
    "><?php echo htmlspecialchars($pageTitle); ?></h2>

    <p style="margin-bottom: 30px; font-size: 1.1em;">
        Selamat datang di area administrasi, <span style="font-weight: bold; color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;"><?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?></span>!
    </p>

    <ul style="
        list-style-type: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 15px; /* Space between list items */
        align-items: center; /* Center the links */
    ">
        <li style="width: 100%;">
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="
                display: block; /* Make the link fill the list item width */
                font-size: 1.2em;
                text-decoration: none;
                color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                padding: 15px 20px;
                border: 1px solid <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                border-radius: 8px;
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                font-weight: 600;
            " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>'; this.style.color='<?php echo htmlspecialchars($paletteWhite); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='<?php echo htmlspecialchars($paletteMediumBlue); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteMediumBlue); ?>';">
                Kelola Data Kos
            </a>
        </li>
        <li style="width: 100%;">
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="
                display: block;
                font-size: 1.2em;
                text-decoration: none;
                color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                padding: 15px 20px;
                border: 1px solid <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                border-radius: 8px;
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                font-weight: 600;
            " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>'; this.style.color='<?php echo htmlspecialchars($paletteWhite); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='<?php echo htmlspecialchars($paletteMediumBlue); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteMediumBlue); ?>';">
                Kelola Pengguna
            </a>
        </li>
        <li style="width: 100%;">
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" style="
                display: block;
                font-size: 1.2em;
                text-decoration: none;
                color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                padding: 15px 20px;
                border: 1px solid <?php echo htmlspecialchars($paletteMediumBlue); ?>;
                border-radius: 8px;
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                font-weight: 600;
            " onmouseover="this.style.backgroundColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>'; this.style.color='<?php echo htmlspecialchars($paletteWhite); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteDarkBlue); ?>';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='<?php echo htmlspecialchars($paletteMediumBlue); ?>'; this.style.borderColor='<?php echo htmlspecialchars($paletteMediumBlue); ?>';">
                Kelola Pemesanan
            </a>
        </li>
    </ul>
</div>