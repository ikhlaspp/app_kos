<?php
// views/kos/detail.php
// Variables $pageTitle, $appConfig, $kos are available.

// Custom Color Palette for consistency
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';
$paletteMediumBlue = '#4A90E2'; // Used for primary accents/buttons, price
$paletteDarkBlue = '#1A3A5B';   // Used for darker text/hover states, main headings
$paletteTextPrimary = '#0D2A57'; // Main text color
$paletteTextSecondary = '#555555'; // Secondary text color (e.g., address)
$paletteAccentBlue = '#6A9EFF'; // For lighter blue accents if needed

// Status Colors - map to your palette or similar
$statusSuccess = '#28a745';    // Green for available
$statusWarning = '#ffc107';    // Yellow for pending/maintenance
$statusDanger = '#dc3545';     // Red for booked/rejected
$statusInfo = '#17a2b8';       // Teal/Light Blue for completed

// Helper to adjust color brightness (used for borders/accents)
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

<style>
    /* Main Page Title */
    h2 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    /* Main Detail Card Container */
    .kos-detail-card {
        background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    /* Image Gallery Styles */
    .kos-gallery img {
        width: 100%; /* Make images responsive */
        max-width: 200px; /* Max size for individual thumbnails */
        height: 150px; /* Fixed height for consistency */
        object-fit: cover; /* Cover the area, cropping if necessary */
        border-radius: 6px;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
        margin-bottom: 10px;
        cursor: pointer; /* Indicate it's clickable */
        transition: transform 0.2s ease-in-out; /* Add transition for hover effect */
    }
    .kos-gallery img:hover {
        transform: scale(1.03); /* Slightly enlarge on hover */
    }

    .kos-main-image {
        width: 100%;
        max-height: 400px; /* Max height for main image */
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
        margin-bottom: 1.5rem;
    }
    .kos-no-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
        margin-bottom: 1.5rem;
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>;
        display: flex;
        align-items: center;
        justify-content: center;
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
        font-size: 1.2rem;
    }

    /* Headings within card */
    .kos-detail-card h3, .kos-detail-card h4 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .kos-detail-card h4 {
        margin-top: 1.5rem; /* More space above sub-sections */
    }

    /* Paragraph Text */
    .kos-detail-card p {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
        line-height: 1.6;
        margin-bottom: 0.5rem;
    }
    .kos-detail-card p strong {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    }

    /* Price Styling */
    .kos-price-display {
        font-size: 1.5em;
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        font-weight: bold;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    /* Status Badges */
    .status-badge {
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 0.35rem;
        display: inline-block;
        vertical-align: middle;
        margin-left: 5px;
    }
    .status-badge.bg-available { background-color: <?php echo htmlspecialchars($statusSuccess); ?>; color: <?php echo htmlspecialchars($paletteWhite); ?>; }
    .status-badge.bg-booked { background-color: <?php echo htmlspecialchars($statusDanger); ?>; color: <?php echo htmlspecialchars($paletteWhite); ?>; }
    .status-badge.bg-maintenance { background-color: <?php echo htmlspecialchars($statusWarning); ?>; color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -100)); ?>; } /* Darker text for yellow */

    /* Action Buttons / Messages */
    .btn-custom-cta {
        display: inline-block;
        padding: 12px 25px;
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        text-decoration: none;
        border-radius: 50px; /* Pill shape */
        margin-top: 1.5rem;
        font-size: 1.1em;
        font-weight: 600;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-custom-cta:hover {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    /* Info/Warning/Danger Messages */
    .kos-message {
        padding: 1rem 1.5rem;
        border-radius: 5px;
        margin-top: 1.5rem;
        font-weight: 500;
    }
    .kos-message.info { background-color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, 100)); ?>; color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, -100)); ?>; border: 1px solid <?php echo htmlspecialchars($statusInfo); ?>; }
    .kos-message.warning { background-color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, 100)); ?>; color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -100)); ?>; border: 1px solid <?php echo htmlspecialchars($statusWarning); ?>; }
    .kos-message.danger { background-color: <?php echo htmlspecialchars(adjustBrightness($statusDanger, 100)); ?>; color: <?php echo htmlspecialchars(adjustBrightness($statusDanger, -100)); ?>; border: 1px solid <?php echo htmlspecialchars($statusDanger); ?>; }


    /* Back Link */
    .back-link {
        display: block;
        margin-top: 2rem;
        text-align: center;
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        text-decoration: none;
        font-weight: 500;
    }
    .back-link:hover {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .kos-detail-card {
            padding: 20px;
        }
        .kos-gallery {
            flex-wrap: wrap;
            justify-content: center;
        }
        .kos-gallery img {
            max-width: 150px;
            height: 120px;
            margin-right: 5px;
            margin-left: 5px;
        }
    }

    /* --- New Modal Styles (added for image pop-up with zoom/pan) --- */
    .image-modal {
        display: none; /* Changed from display: flex; to display: none; */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .image-modal-content-wrapper {
        position: relative; /* For positioning zoom controls */
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }

    .image-modal-content {
        display: block;
        max-width: 90%;
        max-height: 90%;
        object-fit: contain; /* Ensure the whole image fits within the bounds */
        transform: scale(1); /* Initial scale */
        transition: transform 0.2s ease-out; /* Smooth zoom transition */
        cursor: grab; /* Indicate draggable */
    }
    
    .image-modal-content.grabbing {
        cursor: grabbing;
    }

    .image-modal-close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
        z-index: 1001; /* Above other modal elements */
    }

    .image-modal-close:hover,
    .image-modal-close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    .zoom-controls {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 5px;
        padding: 8px 15px;
        display: flex;
        gap: 10px;
        z-index: 1001; /* Above image */
    }

    .zoom-controls button {
        background: none;
        border: 1px solid rgba(255, 255, 255, 0.5);
        color: white;
        font-size: 24px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .zoom-controls button:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    @media (max-width: 767.98px) {
        .zoom-controls {
            bottom: 10px;
            padding: 5px 10px;
            gap: 5px;
        }
        .zoom-controls button {
            font-size: 20px;
            width: 35px;
            height: 35px;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <h2 class="text-center"><?php echo htmlspecialchars($pageTitle ?? 'Detail Kos'); ?></h2>

            <?php if (!empty($kos)): ?>
                <div class="kos-detail-card">
                    <div class="row g-3">
                        <?php if (!empty($kos['gambar_kos']) && is_array($kos['gambar_kos'])): ?>
                            <div class="col-12">
                                <div class="kos-gallery d-flex flex-wrap justify-content-start">
                                    <?php foreach ($kos['gambar_kos'] as $gambar): ?>
                                        <img src="<?php echo htmlspecialchars($appConfig['UPLOADS_URL_PATH'] . $gambar['path']); ?>"
                                             alt="<?php echo htmlspecialchars($gambar['nama_file']); ?>"
                                             class="kos-gallery-img">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif (!empty($kos['gambar_utama'])): /* This case should theoretically not be hit if gambar_kos is populated */ ?>
                            <div class="col-12">
                                <img src="<?php echo htmlspecialchars($appConfig['ASSETS_URL'] . 'images/' . $kos['gambar_utama']); ?>"
                                     alt="Gambar <?php echo htmlspecialchars($kos['nama_kos']); ?>"
                                     class="kos-main-image">
                            </div>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="kos-no-image" style="height: 250px;">
                                    <span>Tidak ada gambar</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <h3><?php echo htmlspecialchars($kos['nama_kos']); ?></h3>
                            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($kos['alamat']); ?></p>
                            <p class="kos-price-display">Harga: Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?> / bulan</p>
                            
                            <p>
                                <strong>Status:</strong> 
                                <?php
                                    $status_kos_view = $kos['status_kos'] ?? 'maintenance';
                                    $statusClass = '';
                                    switch ($status_kos_view) {
                                        case 'available': $statusClass = 'bg-available'; break;
                                        case 'booked': $statusClass = 'bg-booked'; break;
                                        case 'maintenance': $statusClass = 'bg-maintenance'; break;
                                        default: $statusClass = 'bg-secondary'; break;
                                    }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo ucfirst(htmlspecialchars($status_kos_view)); ?>
                                </span>
                            </p>
                            <p>
                                <strong>Kamar Tersedia:</strong> 
                                <?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?> dari <?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?> unit
                            </p>
                            
                            <h4>Deskripsi:</h4>
                            <p><?php echo nl2br(htmlspecialchars($kos['deskripsi'] ?? 'Tidak ada deskripsi.')); ?></p>
                            
                            <h4>Fasilitas:</h4>
                            <p><?php echo htmlspecialchars($kos['fasilitas_kos'] ?? 'Tidak ada informasi fasilitas.'); ?></p>

                            <?php if ($kos['status_kos'] === 'available' && ($kos['jumlah_kamar_tersedia'] ?? 0) > 0): ?>
                                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'booking/pesan/' . $kos['id']); ?>" 
                                    class="btn-custom-cta">
                                    Pesan Sekarang!
                                </a>
                            <?php elseif (($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kos['status_kos'] === 'booked'): ?>
                                <p class="kos-message warning">
                                    Semua kamar sudah terpesan atau kos penuh.
                                </p>
                            <?php else: ?>
                                <p class="kos-message info">
                                    Saat ini tidak tersedia untuk dipesan (<?php echo htmlspecialchars($kos['status_kos']); ?>).
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="alert alert-info text-center">Detail kos tidak ditemukan atau tidak valid.</p>
            <?php endif; ?>

            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>" class="back-link">
                Kembali ke Daftar Kos
            </a>
        </div>
    </div>
</div>

<div id="imageModal" class="image-modal">
    <span class="image-modal-close">&times;</span>
    <div class="image-modal-content-wrapper">
        <img class="image-modal-content" id="modalImage">
        <div class="zoom-controls">
            <button id="zoomOutBtn">-</button>
            <button id="zoomInBtn">+</button>
        </div>
    </div>
</div>

<script>
    // Get the modal elements
    var modal = document.getElementById("imageModal");
    var modalImg = document.getElementById("modalImage");
    var closeBtn = document.getElementsByClassName("image-modal-close")[0];
    var zoomInBtn = document.getElementById("zoomInBtn");
    var zoomOutBtn = document.getElementById("zoomOutBtn");

    // Get all images with the class 'kos-gallery-img'
    var galleryImages = document.querySelectorAll(".kos-gallery-img");

    // Current zoom level and position for panning
    var currentZoom = 1;
    var minZoom = 0.5; // Minimum zoom out level
    var maxZoom = 3;   // Maximum zoom in level
    var zoomStep = 0.2; // How much to zoom per click

    var isDragging = false;
    var startX, startY;
    var currentX = 0, currentY = 0; // Current translation (pan position)

    // Function to apply zoom and translation (pan)
    function applyTransform() {
        modalImg.style.transform = `scale(${currentZoom}) translate(${currentX}px, ${currentY}px)`;
    }

    // Reset zoom and position when opening a new image
    function resetImageState() {
        currentZoom = 1;
        currentX = 0;
        currentY = 0;
        applyTransform();
    }

    // Loop through each gallery image and add an onclick event listener
    galleryImages.forEach(function(img) {
        img.onclick = function() {
            modal.style.display = "flex"; // Make modal visible and use flex for centering
            modalImg.src = this.src; // Set the source of the large image
            
            // Reset zoom and position for the new image
            resetImageState();
        }
    });

    // When the user clicks on the close button (x), hide the modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the image (on the modal background), hide the modal
    modal.onclick = function(event) {
        // Only close if click is on the modal overlay itself, not on the image or controls
        if (event.target == modal || event.target.classList.contains('image-modal-content-wrapper')) { 
            modal.style.display = "none";
        }
    }

    // Zoom in functionality
    zoomInBtn.onclick = function() {
        if (currentZoom < maxZoom) {
            currentZoom = Math.min(maxZoom, currentZoom + zoomStep);
            applyTransform();
        }
    }

    // Zoom out functionality
    zoomOutBtn.onclick = function() {
        if (currentZoom > minZoom) {
            currentZoom = Math.max(minZoom, currentZoom - zoomStep);
            applyTransform();
        }
    }

    // --- Drag/Pan functionality for the image ---
    modalImg.addEventListener('mousedown', (e) => {
        // Only allow dragging if the image is actually zoomed in
        if (currentZoom > 1) { 
            isDragging = true;
            modalImg.classList.add('grabbing'); // Change cursor style
            // Calculate initial offset from image's current position
            const transform = modalImg.style.transform;
            const match = transform.match(/translate\(([-\d.]+)px, ([-\d.]+)px\)/);
            if (match) {
                currentX = parseFloat(match[1]);
                currentY = parseFloat(match[2]);
            }
            startX = e.clientX - currentX;
            startY = e.clientY - currentY;
            e.preventDefault(); // Prevent default browser drag behavior
        }
    });

    modalImg.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        currentX = e.clientX - startX;
        currentY = e.clientY - startY;
        applyTransform();
    });

    modalImg.addEventListener('mouseup', () => {
        isDragging = false;
        modalImg.classList.remove('grabbing');
    });

    modalImg.addEventListener('mouseleave', () => {
        isDragging = false;
        modalImg.classList.remove('grabbing');
    });

    // Optional: Mouse wheel zoom
    modalImg.addEventListener('wheel', (e) => {
        e.preventDefault(); // Prevent page scroll
        const scaleAmount = e.deltaY * -0.005; // Adjust sensitivity for smoother scroll zoom
        const newZoom = currentZoom + scaleAmount;

        if (newZoom >= minZoom && newZoom <= maxZoom) {
            currentZoom = newZoom;
            applyTransform();
        }
    });

</script>