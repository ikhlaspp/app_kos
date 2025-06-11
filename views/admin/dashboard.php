<?php
// File: views/admin/dashboard.php
// Assumes $totalUsers, $totalKos, $totalBookings, $pendingBookings, $recentConfirmedBookings, $recentLogs are available.
// Assumes $_SESSION['user_nama'] and $appConfig are available.

// Custom Color Palette for consistency
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';   // General background for light sections, hover states
$paletteMediumBlue = '#4A90E2'; // Primary accent color, active states, links
$paletteDarkBlue = '#1A3A5B';   // Darker accent color, main headers, primary text
$paletteTextPrimary = '#0D2A57'; // Main body text color
$paletteTextSecondary = '#555555'; // Secondary text for smaller text

// Status Colors for quick nav links (reusing for consistency)
$navColorUsers = '#17a2b8'; // Info/Teal
$navColorKos = '#007bff';   // Primary Blue
$navColorBookings = '#28a745'; // Success Green
$navColorVouchers = '#FF9F0E'; // Orange/Warning


// Helper to adjust color brightness (used for borders/accents)
// function adjustBrightness($hex, $steps) {
//     $steps = max(-255, min(255, $steps));
//     $hex = str_replace('#', '', $hex);
//     $rgb = [];
//     if (strlen($hex) == 3) {
//         $rgb[0] = hexdec(str_repeat(substr($hex, 0, 1), 2));
//         $rgb[1] = hexdec(str_repeat(substr($hex, 1, 1), 2));
//         $rgb[2] = hexdec(str_repeat(substr($hex, 2, 1), 2));
//     } else {
//         $rgb[0] = hexdec(substr($hex, 0, 2));
//         $rgb[1] = hexdec(substr($hex, 2, 2));
//         $rgb[2] = hexdec(substr($hex, 4, 2));
//     }
//     $rgb[0] = max(0, min(255, $rgb[0] + $steps));
//     $rgb[1] = max(0, min(255, $rgb[1] + $steps));
//     $rgb[2] = max(0, min(255, $rgb[2] + $steps));
//     return '#' . str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT)
//                  . str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT)
//                  . str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);
// }
?>
<style>
    /* Dashboard Cards - KEPT AS IS (User's request) */
    .dashboard-cards {
        display: grid;
        /* Adjusted for responsiveness: auto-fill columns, min-width 240px, flexible grow */
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .dashboard-card {
        color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 150px;
    }
    .dashboard-card .card-title {
        font-size: 1.1em;
        font-weight: 500;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    .dashboard-card .card-count {
        font-size: 2.8em;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 10px;
        color: #ffffff;
    }
    .dashboard-card .card-link a {
        font-size: 0.9em;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        display: inline-block;
        margin-top: auto;
    }
    .dashboard-card .card-link a:hover {
        color: #fff;
        text-decoration: underline;
    }
    /* Original Card Colors (User's request to keep) */
    .card-color-1 { background-color: #4747A1; }
    .card-color-2 { background-color: #F95F53; }
    .card-color-3 { background-color: #25A689; }
    .card-color-4 { background-color: #FF9F0E; }


    /* Dashboard Greeting Section */
    .dashboard-greeting {
        margin-bottom: 25px;
        padding-bottom:15px;
        border-bottom:1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
    }
    .dashboard-greeting h3 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .dashboard-greeting p {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }

    /* Chart Container - FIXED MAX HEIGHT */
    .chart-container {
        margin-bottom: 30px;
        background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        position: relative; /* Needed for canvas height 100% to work */
        height: 400px; /* FIXED HEIGHT FOR THE CHART CONTAINER */
    }
    .chart-container h4 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 600;
        margin-bottom: 15px;
    }
    /* Canvas element inside chart-container */
    #monthlyBookingChart {
        width: 100% !important; /* Ensure canvas fills container width */
        height: 100% !important; /* Ensure canvas fills container height */
    }


    /* Activity List Sections (Recent Bookings, Audit Log) */
    .activity-section h4 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .activity-list {
        list-style: none;
        padding-left: 0;
    }
    .activity-list li {
        background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        padding: 12px;
        border-bottom: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
        margin-bottom: 8px;
        border-radius: 4px;
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }
    .activity-list li:last-child {
        border-bottom: none;
    }
    .activity-list strong {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    }
    .activity-list small {
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
        display: block;
        margin-top: 3px;
    }
    .activity-list a {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        text-decoration: none;
        font-size: 0.85em;
        margin-left: 10px;
    }
    .activity-list a:hover {
        color: #ffffff; /* This hover color seems off for a link, consider if it's intentional */
        text-decoration: underline;
    }
    .activity-section > p {
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
    }


    /* Flex container for sections */
    .dashboard-sections-flex {
        display: flex;
        flex-wrap: wrap; /* Allows sections to wrap to the next line */
        gap: 30px;
        margin-top: 30px;
    }
    .dashboard-sections-flex > section {
        flex: 1;
        min-width: 320px; /* Ensures sections don't get too small before wrapping */
    }

    /* Quick Navigation Links */
    .quick-nav {
        list-style-type: none;
        padding: 0;
        display: flex;
        gap: 15px;
        flex-wrap: wrap; /* Allows links to wrap to the next line */
        margin-top: 15px;
    }
    .quick-nav-link {
        padding: 12px 20px;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        text-decoration: none;
        border-radius: 5px;
        font-size: 1em;
        font-weight: 600;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .quick-nav-link:hover {
        opacity: 0.9;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    /* Specific Quick Nav Colors */
    .quick-nav-link.kos { background-color: <?php echo htmlspecialchars($navColorKos); ?>; }
    .quick-nav-link.users { background-color: <?php echo htmlspecialchars($navColorUsers); ?>; }
    .quick-nav-link.bookings { background-color: <?php echo htmlspecialchars($navColorBookings); ?>; }
    .quick-nav-link.vouchers { background-color: <?php echo htmlspecialchars($navColorVouchers); ?>; }

    /* Responsive adjustments for smaller screens (e.g., mobile) */
    @media (max-width: 767.98px) {
        .dashboard-cards {
            /* On small screens, cards will stack naturally due to minmax(240px, 1fr) and default flex-direction.
               If you want them to always be 100% width on small screens, you could change to:
               grid-template-columns: 1fr;
            */
            gap: 15px; /* Slightly reduced gap for smaller screens */
        }

        .dashboard-sections-flex {
            flex-direction: column; /* Stack sections vertically */
            gap: 20px; /* Adjust gap between stacked sections */
            margin-top: 20px; /* Adjust top margin if needed */
        }
        .dashboard-sections-flex > section {
            min-width: unset; /* Remove min-width constraint */
            width: 100%; /* Make sections take full width */
        }

        .quick-nav {
            flex-direction: column; /* Stack quick nav links vertically */
            align-items: stretch; /* Stretch links to fill width */
            gap: 10px; /* Adjust gap between stacked links */
            margin-top: 20px; /* Adjust top margin if needed */
        }
        .quick-nav-link {
            text-align: center; /* Center text within stacked links */
        }
    }
    /* Small adjustments for very small screens (e.g., older phones) */
    @media (max-width: 480px) {
        .dashboard-card .card-count {
            font-size: 2.2em; /* Slightly smaller font for count on very small screens */
        }
        .chart-container {
            height: 300px; /* Reduce chart height for very small screens */
        }
    }
</style>

<div class="dashboard-greeting">
    <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>!</h3>
    <p>Ini adalah ringkasan aktivitas dan data terbaru dari sistem booking kos Anda.</p>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card card-color-1">
        <p class="card-title">TOTAL PENGGUNA</p>
        <p class="card-count"><?php echo htmlspecialchars($totalUsers ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-2">
        <p class="card-title">TOTAL PROPERTI KOS</p>
        <p class="card-count"><?php echo htmlspecialchars($totalKos ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-3">
        <p class="card-title">TOTAL PESANAN</p> <p class="card-count"><?php echo htmlspecialchars($totalBookings ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-4">
        <p class="card-title">PESANAN PENDING</p>
        <p class="card-count"><?php echo htmlspecialchars($pendingBookings ?? 0); ?></p>
        <?php if (($pendingBookings ?? 0) > 0): ?>
            <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Konfirmasi Pesanan &rarr;</a></p>
        <?php else: ?>
            <p class="card-link"><span style="opacity:0.7;">Tidak ada pesanan pending</span></p>
        <?php endif; ?>
    </div>
</div>

<div class="chart-container">
    <h4>Grafik Pemesanan per Bulan</h4>
    <canvas id="monthlyBookingChart"></canvas>
</div>

<div class="dashboard-sections-flex">
    <section class="activity-section">
        <h4>🗓️ Pemesanan Terkonfirmasi Terbaru</h4>
        <?php if (!empty($recentConfirmedBookings)): ?>
            <ul class="activity-list">
                <?php foreach ($recentConfirmedBookings as $booking): ?>
                    <li>
                        <strong>ID: <?php echo htmlspecialchars($booking['id']); ?></strong> - Kos: <?php echo htmlspecialchars($booking['nama_kos']); ?><br>
                        Penyewa: <?php echo htmlspecialchars($booking['nama_penyewa']); ?> | Total: Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                        <small>Dipesan: <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></small>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['id']); ?>">Lihat Detail</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" class="quick-nav-link kos">Lihat Semua Pemesanan &rarr;</a>
        <?php else: ?>
            <p class="activity-section-text">Belum ada pemesanan terkonfirmasi baru-baru ini.</p>
        <?php endif; ?>
    </section>

    <section class="activity-section">
        <h4>⚠️ Log Audit / Aktivitas Sistem Terbaru</h4>
        <?php if (isset($recentLogs) && !empty($recentLogs)): ?>
            <ul class="activity-list">
                <?php foreach ($recentLogs as $log): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($log['aksi']); ?></strong><br>
                        <small>
                            <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($log['timestamp']))); ?>
                            <?php if (!empty($log['nama_pengguna'])): ?>
                                | Oleh: <?php echo htmlspecialchars($log['nama_pengguna']); ?>
                            <?php elseif(!empty($log['user_id'])): ?>
                                | User ID: <?php echo htmlspecialchars($log['user_id']); ?>
                            <?php else: ?>
                                | Sistem
                            <?php endif; ?>
                        </small>
                        <?php 
                            if (!empty($log['detail_aksi'])) {
                                echo "<br><small>" . htmlspecialchars(substr($log['detail_aksi'],0,100)) . (strlen($log['detail_aksi']) > 100 ? '...' : '') . "</small>";
                            }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="activity-section-text">Belum ada aktivitas tercatat.</p>
        <?php endif; ?>
    </section>
</div>

<hr style="margin: 40px 0; border-top: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;">

<div class="quick-nav">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" class="quick-nav-link kos">Kelola Data Kos</a>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" class="quick-nav-link users">Kelola Pengguna</a>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" class="quick-nav-link bookings">Kelola Pemesanan</a>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/manageVouchers'); ?>" class="quick-nav-link vouchers">Kelola Voucher & Promo</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyBookingChart');
        if (!ctx) {
            console.error('Canvas element #monthlyBookingChart not found.');
            return;
        }

        fetch('<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/getBookingChartData'); ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.labels && data.data) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Jumlah Pemesanan Terkonfirmasi',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(74, 144, 226, 0.8)', // Medium Blue with transparency
                                    'rgba(26, 58, 91, 0.8)',   // Dark Blue with transparency
                                    'rgba(106, 158, 255, 0.8)', // Accent Blue with transparency
                                    // Repeat or define more colors as needed
                                    'rgba(74, 144, 226, 0.8)', 'rgba(26, 58, 91, 0.8)', 'rgba(106, 158, 255, 0.8)',
                                    'rgba(74, 144, 226, 0.8)', 'rgba(26, 58, 91, 0.8)', 'rgba(106, 158, 255, 0.8)',
                                    'rgba(74, 144, 226, 0.8)', 'rgba(26, 58, 91, 0.8)', 'rgba(106, 158, 255, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(74, 144, 226, 1)',
                                    'rgba(26, 58, 91, 1)',
                                    'rgba(106, 158, 255, 1)',
                                    // Repeat for borders
                                    'rgba(74, 144, 226, 1)', 'rgba(26, 58, 91, 1)', 'rgba(106, 158, 255, 1)',
                                    'rgba(74, 144, 226, 1)', 'rgba(26, 58, 91, 1)', 'rgba(106, 158, 255, 1)',
                                    'rgba(74, 144, 226, 1)', 'rgba(26, 58, 91, 1)', 'rgba(106, 158, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Pemesanan',
                                        color: '<?php echo htmlspecialchars($paletteTextPrimary); ?>'
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        color: '<?php echo htmlspecialchars($paletteTextSecondary); ?>'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        color: '<?php echo htmlspecialchars($paletteTextPrimary); ?>'
                                    },
                                    ticks: {
                                        color: '<?php echo htmlspecialchars($paletteTextSecondary); ?>'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: '<?php echo htmlspecialchars($paletteTextPrimary); ?>'
                                    }
                                },
                                title: {
                                    display: false,
                                }
                            }
                        }
                    });
                } else {
                    console.warn('API returned no chart data:', data);
                    ctx.parentNode.innerHTML = '<p class="text-muted text-center mt-3">Tidak ada data pemesanan terkonfirmasi untuk grafik.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                ctx.parentNode.innerHTML = '<p class="text-danger text-center mt-3">Gagal memuat grafik pemesanan. Coba lagi nanti.</p>';
            });
    });
</script>