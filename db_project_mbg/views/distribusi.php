<?php
require_once __DIR__ . '/../controllers/DistribusiController.php';
$distCtrl = new DistribusiController();
$msg = '';

$user_role = $_SESSION['role'] ?? 'admin'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $distCtrl->create(
            $_POST['id_sekolah'], 
            $_POST['id_menu'], 
            $_POST['tanggal_distribusi'], 
            $_POST['jumlah_porsi'], 
            $_POST['id_user'] 
        );
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        $res = $distCtrl->update(
            $_POST['id_distribusi'],
            $_POST['id_sekolah'], 
            $_POST['id_menu'], 
            $_POST['tanggal_distribusi'], 
            $_POST['jumlah_porsi'], 
            $_POST['id_user'],
            $_POST['status'] 
        );
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'delete') {
        $res = $distCtrl->delete($_POST['id_distribusi']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'complete') {
        if (method_exists($distCtrl, 'complete')) {
            $res = $distCtrl->complete($_POST['id_distribusi']);
        } else {
            $res = $distCtrl->updateStatus($_POST['id_distribusi'], 'selesai');
        }
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$list_distribusi = $distCtrl->readAll();
$database = new Database();
$dbConn = $database->getConnection();
$list_sekolah = $dbConn->query("SELECT id_sekolah, nama_sekolah FROM sekolah ORDER BY nama_sekolah ASC")->fetchAll(PDO::FETCH_ASSOC);
$list_menu = $dbConn->query("SELECT id_menu, nama_menu FROM menu_makanan WHERE status='aktif' ORDER BY nama_menu ASC")->fetchAll(PDO::FETCH_ASSOC);
$list_operator = $dbConn->query("SELECT id_users, nama FROM users WHERE role != 'admin' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .mbg-title { color: #061c1d; font-weight: 700; }
    .btn-mbg-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important; border: none !important; font-weight: 600;
        border-radius: 10px; padding: 10px 20px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.25s ease;
    }
    .btn-mbg-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.3); color: #ffffff !important; }
    .mbg-table-card { border-radius: 16px !important; border: 1px solid rgba(0, 0, 0, 0.03) !important; box-shadow: 0 10px 30px rgba(6, 28, 29, 0.02) !important; background: #ffffff; overflow: hidden; }
    .mbg-table-header { background: #ffffff !important; border-bottom: 2px solid #f1f5f9 !important; padding: 20px 24px; }
    .table th { font-size: 0.8rem; text-uppercase: true; letter-spacing: 0.5px; color: #64748b; padding: 16px 12px !important; }
    .table td { padding: 16px 12px !important; color: #334155; }
    .badge-porsi-box { background: rgba(6, 28, 29, 0.06) !important; color: #061c1d !important; border: 1px solid rgba(6, 28, 29, 0.1); font-weight: 700; padding: 6px 14px; border-radius: 8px; }
    .badge-status-selesai { background: rgba(16, 185, 129, 0.15) !important; color: #059669 !important; font-weight: 700; padding: 6px 14px; border-radius: 30px; font-size: 0.75rem; display: inline-block; }
    .badge-status-proses { background: rgba(245, 158, 11, 0.15) !important; color: #d97706 !important; font-weight: 700; padding: 6px 14px; border-radius: 30px; font-size: 0.75rem; display: inline-block; }
    .modal-content { border-radius: 16px !important; border: none !important; box-shadow: 0 20px 50px rgba(6, 28, 29, 0.15) !important; }
    .modal-header { background: #061c1d !important; color: #ffffff !important; border-bottom: none !important; padding: 20px 24px !important; }
    .modal-header .btn-close { filter: invert(1) grayscale(1) brightness(2); }
    .form-control, .form-select { border-radius: 10px !important; padding: 10px 14px; border: 1px solid #e2e8f0; }
    .form-control:focus, .form-select:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
</style>

<div class="container-fluid pt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mbg-title mb-1">Pengiriman & Distribusi Sekolah</h3>
            <p class="text-muted small mb-0">Log real-time pencatatan pengiriman paket makanan bergizi harian ke tiap wilayah sekolah.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalDistribusi">
            <i class="fas fa-truck-ramp-box me-2"></i> Kirim Paket Baru
        </button>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-boxes-packing text-success me-2"></i> Manifest Log Distribusi Terkirim
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 15%;">Tanggal Pengiriman</th>
                        <th style="width: 25%;">Institusi Sekolah Tujuan</th>
                        <th style="width: 25%;">Paket Menu Pangan</th>
                        <th class="text-center" style="width: 12%;">Jumlah Distribusi</th>
                        <th style="width: 13%;">Kurir / Operator Lapangan</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($list_distribusi) > 0): ?>
                        <?php foreach($list_distribusi as $d): ?>
                        <tr>
                            <td class="ps-4 font-monospace small fw-semibold text-secondary">
                                <i class="far fa-calendar-check text-muted me-2"></i><?= date('d M Y', strtotime($d['tanggal_distribusi'])); ?>
                            </td>
                            <td class="fw-bold" style="color: #061c1d;">
                                <i class="fas fa-school text-secondary opacity-50 me-2"></i><?= htmlspecialchars($d['nama_sekolah']); ?>
                            </td>
                            <td class="fw-medium text-secondary">
                                <i class="fas fa-utensils text-success opacity-75 me-2"></i><?= htmlspecialchars($d['nama_menu']); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge-porsi-box font-monospace">
                                    <?= number_format($d['jumlah_porsi']); ?> Box
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="far fa-user-circle me-1"></i> <?= htmlspecialchars($d['nama_petugas']); ?>
                            </td>
                            <td class="text-center">
                                <?php if (isset($d['status']) && $d['status'] === 'selesai'): ?>
                                    <span class="badge-status-selesai"><i class="fas fa-circle-check me-1"></i> Selesai</span>
                                <?php else: ?>
                                    <span class="badge-status-proses"><i class="fas fa-truck-fast me-1"></i> Proses</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1 w-100">
                                    
                                    <?php if (!isset($d['status']) || $d['status'] !== 'selesai'): ?>
                                        <form method="POST" action="" onsubmit="return confirm('Tandai distribusi ini sebagai selesai?');" class="m-0">
                                            <input type="hidden" name="action" value="complete">
                                            <input type="hidden" name="id_distribusi" value="<?= $d['id_distribusi']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success px-2 rounded-3 text-white fw-semibold shadow-sm" style="font-size: 0.78rem;">
                                                <i class="fas fa-check-double me-1"></i>Selesai
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <button class="btn btn-sm btn-light border text-warning px-2 rounded-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $d['id_distribusi']; ?>">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>

                                    <form method="POST" action="" onsubmit="return confirm('Hapus data rekaman distribusi ini?');" class="m-0">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_distribusi" value="<?= $d['id_distribusi']; ?>">
                                        <button type="submit" class="btn btn-sm btn-light border text-danger px-2 rounded-3 shadow-sm">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="modal fade" id="modalEdit<?= $d['id_distribusi']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="" class="modal-content text-start">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id_distribusi" value="<?= $d['id_distribusi']; ?>">
                                            
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold text-white">
                                                    <i class="fas fa-truck-moving me-2 text-warning"></i> Edit Manifest Pengiriman Data
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Institusi Sekolah Penerima Manfaat</label>
                                                    <select name="id_sekolah" class="form-select" required>
                                                        <?php foreach($list_sekolah as $s): ?>
                                                            <option value="<?= $s['id_sekolah']; ?>" <?= $s['id_sekolah'] == $d['id_sekolah'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($s['nama_sekolah']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Paket Menu Makanan (Terverifikasi Gizi)</label>
                                                    <select name="id_menu" class="form-select" required>
                                                        <?php foreach($list_menu as $m): ?>
                                                            <option value="<?= $m['id_menu']; ?>" <?= $m['id_menu'] == $d['id_menu'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($m['nama_menu']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Kurir / Operator Lapangan</label>
                                                    <select name="id_user" class="form-select" required>
                                                        <?php foreach($list_operator as $op): ?>
                                                            <option value="<?= $op['id_users']; ?>" <?= $op['id_users'] == $d['id_users'] ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($op['nama']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Status Pengiriman Manifest</label>
                                                    <select name="status" class="form-select fw-semibold" required>
                                                        <option value="proses" <?= (isset($d['status']) && $d['status'] === 'proses') ? 'selected' : ''; ?>>🔄 Dalam Proses (Proses)</option>
                                                        <option value="selesai" <?= (isset($d['status']) && $d['status'] === 'selesai') ? 'selected' : ''; ?>>✅ Selesai Diterima (Selesai)</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Tanggal Pengiriman</label>
                                                        <input type="date" name="tanggal_distribusi" class="form-control font-monospace" value="<?= $d['tanggal_distribusi']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Jumlah Total Porsi</label>
                                                        <div class="input-group">
                                                            <input type="number" name="jumlah_porsi" class="form-control font-monospace" value="<?= $d['jumlah_porsi']; ?>" min="1" required>
                                                            <span class="input-group-text bg-light text-muted small">Box</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                                                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-mbg-primary px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-truck-arrow-right d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada riwayat manifes pengiriman menu makanan harian hari ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDistribusi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-white">
                    <i class="fas fa-paper-plane me-2 text-success"></i> Form Registrasi Distribusi Pangan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Institusi Sekolah Penerima Manfaat</label>
                    <select name="id_sekolah" class="form-select" required>
                        <option value="">-- Pilih Wilayah/Sekolah Sasaran --</option>
                        <?php foreach($list_sekolah as $s): ?>
                            <option value="<?= $s['id_sekolah']; ?>"><?= htmlspecialchars($s['nama_sekolah']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Paket Menu Makanan (Terverifikasi Gizi)</label>
                    <select name="id_menu" class="form-select" required>
                        <option value="">-- Pilih Formula Menu Aktif --</option>
                        <?php foreach($list_menu as $m): ?>
                            <option value="<?= $m['id_menu']; ?>"><?= htmlspecialchars($m['nama_menu']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Kurir / Operator Lapangan</label>
                    <select name="id_user" class="form-select" required>
                        <option value="">-- Pilih Petugas Pengirim --</option>
                        <?php foreach($list_operator as $op): ?>
                            <option value="<?= $op['id_users']; ?>"><?= htmlspecialchars($op['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Tanggal Pengiriman</label>
                        <input type="date" name="tanggal_distribusi" class="form-control font-monospace" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah Total Porsi</label>
                        <div class="input-group">
                            <input type="number" name="jumlah_porsi" class="form-control font-monospace" placeholder="0" min="1" required>
                            <span class="input-group-text bg-light text-secondary small font-monospace">Box</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Eksekusi Pengiriman</button>
            </div>
        </form>
    </div>
</div>