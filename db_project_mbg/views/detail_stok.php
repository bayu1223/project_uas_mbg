<?php
require_once __DIR__ . '/../controllers/DetailStokController.php';
$stokCtrl = new DetailStokController();
$msg = '';

// Mengambil role dari session (Default ke petugas jika tidak ada)
$user_role = $_SESSION['role'] ?? 'petugas'; 

// ==========================================
// PROSES BACKEND (HANYA UNTUK ADMIN)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Validasi Hard-Security: Jika bukan admin tetapi mencoba kirim POST, blokir!
    if ($user_role !== 'admin') {
        $msg = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3'>⚠️ <strong>Akses Ditolak:</strong> Petugas tidak memiliki izin untuk memodifikasi data.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        // ACTION: TAMBAH DATA
        if ($_POST['action'] === 'add') {
            $res = $stokCtrl->create(
                $_POST['id_stok'],
                $_POST['jenis_transaksi'],
                $_POST['jumlah'],
                Database::sanitize($_POST['keterangan']),
                $_SESSION['user_id'] 
            );
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
        
        // ACTION: EDIT DATA (Baru)
        if ($_POST['action'] === 'edit') {
            // Pastikan method update($id_detail, $id_stok, $jenis, $jumlah, $keterangan) tersedia di Controller Anda
            $res = $stokCtrl->update(
                $_POST['id_detail'],
                $_POST['id_stok'],
                $_POST['jenis_transaksi'],
                $_POST['jumlah'],
                Database::sanitize($_POST['keterangan'])
            );
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }

        // ACTION: HAPUS DATA (Baru)
        if ($_POST['action'] === 'delete') {
            // Pastikan method delete($id_detail) tersedia di Controller Anda
            $res = $stokCtrl->delete($_POST['id_detail']);
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

$bahan_baku = $stokCtrl->readAll();
$database = new Database();
$dbConn = $database->getConnection();
$list_bahan = $dbConn->query("SELECT id_stok, nama_bahan, jumlah, satuan FROM stok_bahan ORDER BY nama_bahan ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .mbg-title { color: #061c1d; font-weight: 700; }
    .btn-mbg-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: #ffffff !important; border: none !important; font-weight: 600; border-radius: 10px; padding: 10px 20px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: all 0.25s ease; }
    .btn-mbg-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.3); color: #ffffff !important; }
    .mbg-table-card { border-radius: 16px !important; border: 1px solid rgba(0, 0, 0, 0.03) !important; box-shadow: 0 10px 30px rgba(6, 28, 29, 0.02) !important; background: #ffffff; overflow: hidden; }
    .mbg-table-header { background: #ffffff !important; border-bottom: 2px solid #f1f5f9 !important; padding: 20px 24px; }
    .table th { font-size: 0.8rem; text-uppercase: true; letter-spacing: 0.5px; color: #64748b; padding: 16px 12px !important; }
    .table td { padding: 16px 12px !important; color: #334155; }
    .badge-masuk { background: rgba(16, 185, 129, 0.15) !important; color: #059669 !important; font-weight: 700; padding: 5px 12px; border-radius: 30px; font-size: 0.75rem; }
    .badge-keluar { background: rgba(239, 68, 68, 0.15) !important; color: #dc2626 !important; font-weight: 700; padding: 5px 12px; border-radius: 30px; font-size: 0.75rem; }
    .badge-volume { background: rgba(6, 28, 29, 0.06) !important; color: #061c1d !important; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
    .modal-content { border-radius: 16px !important; border: none !important; box-shadow: 0 20px 50px rgba(6, 28, 29, 0.15) !important; }
    .modal-header { background: #061c1d !important; color: #ffffff !important; border-bottom: none !important; padding: 20px 24px !important; }
    .modal-header .btn-close { filter: invert(1) grayscale(1) brightness(2); }
    .form-control, .form-select { border-radius: 10px !important; padding: 10px 14px; border: 1px solid #e2e8f0; }
    .form-control:focus, .form-select:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
</style>

<div class="container-fluid pt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mbg-title mb-1">Logistik & Transaksi Mutasi Stok</h3>
            <p class="text-muted small mb-0">Pantau seluruh aliran keluar masuk bahan baku dapur pusat pemenuhan gizi.</p>
        </div>
        <?php if ($user_role === 'admin'): ?>
            <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-dolly me-2"></i> Catat Transaksi Mutasi
            </button>
        <?php endif; ?>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-boxes-stacked text-success me-2"></i> Jurnal Riwayat Aliran Stok
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start ps-4">Waktu Input</th>
                        <th class="text-start">Nama Bahan</th>
                        <th>Jenis Transaksi</th>
                        <th>Volume Modifikasi</th>
                        <th class="text-start">Keterangan Deskriptif</th>
                        <th>Operator Lapangan</th>
                        <?php if ($user_role === 'admin'): ?>
                        <th class="pe-4">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bahan_baku) > 0): ?>
                        <?php foreach($bahan_baku as $row): ?>
                        <tr>
                            <td class="text-start ps-4 font-monospace small text-secondary">
                                <?= date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?>
                            </td>
                            <td class="text-start fw-bold" style="color: #061c1d;">
                                <?= htmlspecialchars($row['nama_bahan']); ?>
                            </td>
                            <td>
                                <span class="<?= $row['jenis_transaksi'] == 'masuk' ? 'badge-masuk' : 'badge-keluar'; ?>">
                                    <i class="fas <?= $row['jenis_transaksi'] == 'masuk' ? 'fa-arrow-down-long' : 'fa-arrow-up-long'; ?> me-1"></i>
                                    <?= strtoupper($row['jenis_transaksi']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-volume font-monospace">
                                    <?= $row['jumlah']; ?>
                                </span>
                            </td>
                            <td class="text-start text-muted small" style="max-width: 240px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                <?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '<span class="text-opacity-25">-</span>'; ?>
                            </td>
                            <td class="text-secondary fw-medium">
                                <i class="fas fa-user-circle me-1 opacity-50"></i><?= htmlspecialchars($row['nama_petugas']); ?>
                            </td>
                            
                            <?php if ($user_role === 'admin'): ?>
                                <div class="hidden-data-<?= $row['id_detail_stok']; ?>" 
                                     data-idstok="<?= $row['id_stok']; ?>"
                                     data-jenis="<?= $row['jenis_transaksi']; ?>"
                                     data-jumlah="<?= $row['jumlah']; ?>"
                                     data-ket="<?= htmlspecialchars($row['keterangan']); ?>" style="display:none;"></div>
                                <td class="pe-4">
                                    <button class="btn btn-sm btn-outline-warning rounded-2 me-1 btn-edit-trigger" data-id="<?= $row['id_detail_stok']; ?>" title="Ubah Data">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-2 btn-hapus-trigger" data-id="<?= $row['id_detail_stok']; ?>" title="Hapus Permanen">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $user_role === 'admin' ? '7' : '6'; ?>" class="text-center text-muted py-5">
                                <i class="fas fa-clipboard-list d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada riwayat mutasi logistik yang tercatat hari ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($user_role === 'admin'): ?>
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-white"><i class="fas fa-file-invoice me-2 text-success"></i> Catat Mutasi Logistik Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Komoditas Bahan Baku</label>
                    <select name="id_stok" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Bahan Logistik --</option>
                        <?php foreach ($list_bahan as $bahan): ?>
                            <option value="<?= $bahan['id_stok']; ?>"><?= htmlspecialchars($bahan['nama_bahan']); ?> (Stok: <?= $bahan['jumlah']; ?> <?= $bahan['satuan']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Jenis Aliran Mutasi</label>
                    <select name="jenis_transaksi" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Pergerakan --</option>
                        <option value="masuk">MASUK (Suplementasi Pasokan/Penyuplai)</option>
                        <option value="keluar">KELUAR (Distribusi Konsumsi Dapur)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Kuantitas Volume</label>
                    <input type="number" step="any" name="jumlah" class="form-control" placeholder="Masukkan angka kuantitas bahan" min="0.01" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary">Keterangan Tambahan / Berita Acara</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pengiriman dari Supplier X"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-sm btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-mbg-primary shadow-sm px-4">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_detail" id="edit_id_detail">
            <div class="modal-header" style="background: #1e293b !important;">
                <h6 class="modal-title fw-bold text-white"><i class="fas fa-edit me-2 text-warning"></i> Perbarui Data Mutasi Stok</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Komoditas Bahan Baku</label>
                    <select name="id_stok" id="edit_id_stok" class="form-select" required>
                        <?php foreach ($list_bahan as $bahan): ?>
                            <option value="<?= $bahan['id_stok']; ?>"><?= htmlspecialchars($bahan['nama_bahan']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Jenis Aliran Mutasi</label>
                    <select name="jenis_transaksi" id="edit_jenis" class="form-select" required>
                        <option value="masuk">MASUK</option>
                        <option value="keluar">KELUAR</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Kuantitas Volume</label>
                    <input type="number" step="any" name="jumlah" id="edit_jumlah" class="form-control" min="0.01" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-secondary">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4">
                <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-warning shadow-sm px-4 fw-bold text-dark">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" action="" class="modal-content text-center">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_detail" id="delete_id_detail">
            <div class="modal-body p-4">
                <i class="fas fa-exclamation-triangle text-danger display-4 mb-3 d-block"></i>
                <h6 class="fw-bold text-dark mb-2">Hapus Rekam Mutasi?</h6>
                <p class="text-muted small mb-4">Tindakan ini permanen. Nilai kalkulasi stok pada gudang utama akan dikembalikan.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger px-4">Ya, Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modalEditEl = new bootstrap.Modal(document.getElementById('modalEdit'));
    const modalHapusEl = new bootstrap.Modal(document.getElementById('modalHapus'));

    // Event Klik Tombol Edit
    document.querySelectorAll('.btn-edit-trigger').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const container = document.querySelector('.hidden-data-' + id);
            
            document.getElementById('edit_id_detail').value = id;
            document.getElementById('edit_id_stok').value = container.getAttribute('data-idstok');
            document.getElementById('edit_jenis').value = container.getAttribute('data-jenis');
            document.getElementById('edit_jumlah').value = container.getAttribute('data-jumlah');
            document.getElementById('edit_keterangan').value = container.getAttribute('data-ket');
            
            modalEditEl.show();
        });
    });

    // Event Klik Tombol Hapus
    document.querySelectorAll('.btn-hapus-trigger').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_id_detail').value = this.getAttribute('data-id');
            modalHapusEl.show();
        });
    });
});
</script>
<?php endif; ?>