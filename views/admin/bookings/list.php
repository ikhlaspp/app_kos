<?php
// File: views/admin/bookings/list.php
// Assumes $daftarBooking array is passed from AdminController->bookings()
?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
</div>

<?php if (empty($daftarBooking)): ?>
    <div class="alert alert-info" role="alert">
        Belum ada pemesanan yang terdaftar.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr class="table-header-custom">
                    <th>ID Pesanan</th>
                    <th>Penyewa</th>
                    <th>Nama Kos</th>
                    <th>Tanggal Mulai</th>
                    <th>Durasi</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Status Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftarBooking as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['nama_penyewa'] . ' (' . $booking['email_penyewa'] . ')'); ?></td>
                        <td><?php echo htmlspecialchars($booking['nama_kos']); ?></td>
                        <td><?php echo htmlspecialchars($booking['tanggal_mulai']); ?></td>
                        <td><?php echo htmlspecialchars($booking['durasi_sewa']); ?></td>
                        <td>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php
                            $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                            $badgeClass = '';
                            switch ($statusPemesanan) {
                                case 'confirmed': $badgeClass = 'bg-success'; break;
                                case 'pending': $badgeClass = 'bg-warning text-dark'; break;
                                case 'rejected': case 'canceled': $badgeClass = 'bg-danger'; break;
                                case 'completed': $badgeClass = 'bg-info'; break;
                                default: $badgeClass = 'bg-secondary'; break;
                            }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?></span>
                        </td>
                        <td>
                            <?php
                            $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                            $badgeClass = '';
                            switch ($statusPembayaran) {
                                case 'paid': $badgeClass = 'bg-success'; break;
                                case 'pending': $badgeClass = 'bg-warning text-dark'; break;
                                case 'failed': $badgeClass = 'bg-danger'; break;
                                default: $badgeClass = 'bg-secondary'; break;
                            }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['booking_id']); ?>" class="btn btn-sm btn-info">Detail</a>
                            <?php if ($booking['status_pemesanan'] === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-success confirm-booking-btn"
                                        data-bs-id="<?php echo htmlspecialchars($booking['booking_id']); ?>"
                                        data-bs-nama-kos="<?php echo htmlspecialchars($booking['nama_kos']); ?>">
                                    Konfirmasi
                                </button>
                                <button type="button" class="btn btn-sm btn-danger reject-booking-btn"
                                        data-bs-id="<?php echo htmlspecialchars($booking['booking_id']); ?>"
                                        data-bs-nama-kos="<?php echo htmlspecialchars($booking['nama_kos']); ?>">
                                    Tolak
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="modal fade" id="confirmBookingModal" tabindex="-1" aria-labelledby="confirmBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmBookingModalLabel">Konfirmasi Pemesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="confirmBookingForm" method="POST" action="">
                <div class="modal-body">
                    <p>Anda yakin ingin **mengkonfirmasi** pemesanan untuk <strong id="confirmKosNama"></strong> (ID: <strong id="confirmBookingIdDisplay"></strong>)?</p>
                    <input type="hidden" name="booking_id" id="confirmBookingId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectBookingModal" tabindex="-1" aria-labelledby="rejectBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectBookingModalLabel">Tolak Pemesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectBookingForm" method="POST" action="">
                <div class="modal-body">
                    <p>Anda yakin ingin **menolak** pemesanan untuk <strong id="rejectKosNama"></strong> (ID: <strong id="rejectBookingIdDisplay"></strong>)?</p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Alasan Penolakan (Opsional):</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="booking_id" id="rejectBookingId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pemesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?php echo htmlspecialchars($appConfig['BASE_URL']); ?>';

    // Confirmation Modal Logic
    const confirmBookingModal = new bootstrap.Modal(document.getElementById('confirmBookingModal'));
    const confirmBookingButtons = document.querySelectorAll('.confirm-booking-btn');
    confirmBookingButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.bsId;
            const kosNama = this.dataset.bsNamaKos;
            
            document.getElementById('confirmBookingId').value = bookingId;
            document.getElementById('confirmBookingIdDisplay').textContent = bookingId;
            document.getElementById('confirmKosNama').textContent = kosNama;
            document.getElementById('confirmBookingForm').action = `${baseUrl}admin/bookingConfirm/${bookingId}`;
            
            confirmBookingModal.show();
        });
    });

    // Rejection Modal Logic
    const rejectBookingModal = new bootstrap.Modal(document.getElementById('rejectBookingModal'));
    const rejectBookingButtons = document.querySelectorAll('.reject-booking-btn');
    rejectBookingButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.bsId;
            const kosNama = this.dataset.bsNamaKos;
            
            document.getElementById('rejectBookingId').value = bookingId;
            document.getElementById('rejectBookingIdDisplay').textContent = bookingId;
            document.getElementById('rejectKosNama').textContent = kosNama;
            document.getElementById('rejectReason').value = ''; // Clear previous reason
            document.getElementById('rejectBookingForm').action = `${baseUrl}admin/bookingReject/${bookingId}`;
            
            rejectBookingModal.show();
        });
    });
});
</script>