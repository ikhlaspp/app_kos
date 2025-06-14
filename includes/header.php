<?php

$currentAppName = $appConfig['APP_NAME'] ?? 'Nama Aplikasi Default';
$baseUrl = $appConfig['BASE_URL'] ?? '/';
$assetsUrl = $appConfig['ASSETS_URL'] ?? rtrim($baseUrl, '/') . '/assets/';
$currentPageTitle = $pageTitle ?? $currentAppName;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentPageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/style.css">
    <style>
        html {
            height: 100%;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F0F4FF;
            color: #0D2A57;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 60px; /* Adjust based on your header height */
        }
        .page-wrapper {
            flex: 1 0 auto;
        }

        .site-header {
            background-color: #112D4E;
            color: #F9F7F7;
            padding: 12px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            box-sizing: border-box;
            z-index: 1000;
        }

        .site-header .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area a {
            font-size: 1.9rem;
            font-weight: 700;
            color: #F9F7F7;
            text-decoration: none;
            line-height: 1;
        }

        /* Default (desktop) navigation styles */
        .main-navigation {
            margin-left: 25px;
            /* On desktop, main navigation is displayed as a flex row */
            display: flex;
        }
        .main-navigation ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }
        .main-navigation ul li a {
            display: block;
            color: #F9F7F7;
            text-decoration: none;
            padding: 0.6rem 1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }
        .main-navigation ul li a:hover,
        .main-navigation ul li.active a {
            background-color: #3F72AF;
            color: #FFFFFF;
        }

        .header-icons {
            display: flex;
            align-items: center;
            margin-left: auto; /* Push icons to the right */
        }
        .header-icons .icon-link {
            color: #F9F7F7;
            text-decoration: none;
            font-size: 1.25rem;
            margin-left: 18px;
            padding: 5px;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }
        .header-icons .icon-link:hover {
            color: #DBE2EF;
        }

        /* Hide hamburger by default on larger screens */
        .hamburger-menu-placeholder {
            display: none !important; /* Hidden on desktop */
        }

        /* Login/Logout link outside hamburger (desktop-only) */
        .header-auth-link.desktop-only-auth-link {
            display: block; /* Visible on desktop */
            color: #F9F7F7;
            text-decoration: none;
            padding: 0.5rem 0.9rem;
            font-weight: 500;
            font-size: 0.80rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin-left: 15px;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            line-height: 1.2;
            border: 1px solid transparent;
        }
        .header-auth-link.desktop-only-auth-link:hover {
            background-color: #3F72AF;
            color: #FFFFFF;
            border-color: #3F72AF;
        }

        /* Login/Logout link inside hamburger (mobile-only) */
        .main-navigation .mobile-only-auth-link {
            display: none; /* Hidden on desktop within the main nav */
        }

        /* Mobile Styles (<= 992px) */
        @media (max-width: 992px) {
            .main-navigation {
                /* Initially hidden on mobile, will be toggled by JS */
                display: none;
                position: absolute;
                top: 100%; /* Position below header */
                left: 0;
                right: 0;
                background-color: #112D4E;
                box-shadow: 0 8px 16px rgba(0,0,0,0.15);
                z-index: 999;
                padding: 8px 0;
                margin-left: 0; /* Remove default margin */
            }
            .main-navigation.open {
                display: block; /* Show when 'open' class is present */
            }
            .main-navigation ul {
                flex-direction: column; /* Stack menu items vertically */
                width: 100%; /* Ensure ul takes full width of nav */
            }
            .main-navigation ul li {
                text-align: center;
                width: 100%; /* Ensure list items take full width */
            }
            .main-navigation ul li a {
                padding: 12px 15px;
                border-top: 1px solid #3F72AF;
                border-radius: 0;
            }
            .main-navigation ul li:first-child a {
                border-top: none;
            }
            .main-navigation ul li a:hover,
            .main-navigation ul li.active a {
                background-color: #3F72AF;
                color: #FFFFFF;
            }

            /* Show hamburger on mobile screens */
            .hamburger-menu-placeholder {
                display: block !important; /* Visible on mobile */
            }

            /* Hide the desktop login/logout link on mobile */
            .header-auth-link.desktop-only-auth-link {
                display: none;
            }

            /* Style the mobile-only login/logout link when it's inside the mobile menu */
            .main-navigation .mobile-only-auth-link {
                display: block; /* Make sure it's visible within the mobile menu */
                width: 100%; /* Full width */
                margin-left: 0; /* Remove left margin */
                text-align: center; /* Center text */
                padding: 12px 15px; /* Adjust padding to match other menu items */
                border-top: 1px solid #3F72AF; /* Add a border for separation */
                border-radius: 0; /* Remove border-radius */
            }
             .main-navigation .mobile-only-auth-link a.header-auth-link {
                /* Ensure the anchor itself also takes full space and has correct styling */
                display: block;
                width: 100%;
                color: #F9F7F7; /* Adjust color for mobile menu consistency */
                background-color: transparent;
                border: none;
                font-size: 0.85rem; /* Match other menu items */
            }
            .main-navigation .mobile-only-auth-link a.header-auth-link:hover {
                background-color: #3F72AF;
                color: #FFFFFF;
            }
        }

        .main-content-area {
            padding-top: 1.5rem;
            padding-bottom: 2rem;
            flex: 1 0 auto;
        }
        .main-content-area .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        .flash-message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            font-size: 0.95em;
        }
        .flash-message.success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .flash-message.error { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        .flash-message.info { color: #055160; background-color: #cff4fc; border-color: #b6effb; }
        .flash-message.warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="site-header">
            <div class="container">
                <div class="logo-area">
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>"><?php echo htmlspecialchars($currentAppName); ?></a>
                </div>

                <nav class="main-navigation" id="mainNav">
                    <ul>
                        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard Admin</a></li>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>">Dashboard Saya</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                        <?php endif; ?>

                        <!-- Mobile-only Login/Logout Link - visible only when mainNav is open on mobile -->
                        <li class="mobile-only-auth-link">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php $userNameDisplay = $_SESSION['user_nama'] ?? ($_SESSION['is_admin'] ? 'Admin' : 'User'); ?>
                                <a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>" class="header-auth-link">Logout (<?php echo htmlspecialchars($userNameDisplay); ?>)</a>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>" class="header-auth-link">Login</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>

                <div class="header-icons">
                    <!-- Desktop-only Login/Logout Link - visible only on desktop -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php $userNameDisplay = $_SESSION['user_nama'] ?? ($_SESSION['is_admin'] ? 'Admin' : 'User'); ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>" class="header-auth-link desktop-only-auth-link">Logout (<?php echo htmlspecialchars($userNameDisplay); ?>)</a>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>" class="header-auth-link desktop-only-auth-link">Login</a>
                    <?php endif; ?>
                    <!-- Hamburger icon - visible only on mobile -->
                    <a href="#" class="icon-link hamburger-menu-placeholder" id="hamburgerIcon" aria-label="Menu" aria-controls="mainNav" aria-expanded="false">&#9776;</a>
                </div>
            </div>
        </header>

        <main class="main-content-area">
            <div class="container">
                <?php
                if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                    $flash = $_SESSION['flash_message'];
                    echo '<div class="flash-message ' . htmlspecialchars($flash['type'] ?? 'info') . '">';
                    echo htmlspecialchars($flash['message']);
                    echo '</div>';
                    unset($_SESSION['flash_message']);
                }
                ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const mainNav = document.getElementById('mainNav');

        if (hamburgerIcon && mainNav) {
            hamburgerIcon.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default anchor behavior

                // Toggle the 'open' class on the navigation
                mainNav.classList.toggle('open');

                // Toggle aria-expanded attribute for accessibility
                let isExpanded = mainNav.classList.contains('open');
                hamburgerIcon.setAttribute('aria-expanded', isExpanded);
            });

            // Close the navigation when clicking outside of it
            document.addEventListener('click', function(event) {
                // If the click is not on the navigation itself AND not on the hamburger icon
                if (!mainNav.contains(event.target) && !hamburgerIcon.contains(event.target)) {
                    if (mainNav.classList.contains('open')) {
                        mainNav.classList.remove('open');
                        hamburgerIcon.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // Optional: Close menu if a navigation link is clicked (useful for single-page apps or smooth transitions)
            mainNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    // Only close if the menu is currently open
                    if (mainNav.classList.contains('open')) {
                        mainNav.classList.remove('open');
                        hamburgerIcon.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        }
    });
</script>
</body>
</html>