<?php
require_once __DIR__ . '/../controllers/MenuController.php';
$menuCtrl = new MenuController();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $menuCtrl->create(Database::sanitize($_POST['nama_menu']), $_POST['kalori'], $_POST['protein'], $_POST['status']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        $res = $menuCtrl->update($_POST['id_menu'], Database::sanitize($_POST['nama_menu']), $_POST['kalori'], $_POST['protein'], $_POST['status']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'delete') {
        $res = $menuCtrl->delete($_POST['id_menu']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$menus = $menuCtrl->readAll();
?>

<style>
    .mbg-title {
        color: #061c1d;
        font-weight: 700;
    }
    
    /* Tombol Utama Tema MBG (Gradasi Hijau Segar) */
    .btn-mbg-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 20px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.25s ease;
    }
    .btn-mbg-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.3);
        color: #ffffff !important;
    }

    /* Tabel Premium Container */
    .mbg-table-card {
        border-radius: 16px !important;
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
        box-shadow: 0 10px 30px rgba(6, 28, 29, 0.02) !important;
        background: #ffffff;
        overflow: hidden;
    }
    .mbg-table-header {
        background: #ffffff !important;
        border-bottom: 2px solid #f1f5f9 !important;
        padding: 20px 24px;
    }
    .table th {
        font-size: 0.8rem;
        text-uppercase: true;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 16px 12px !important;
    }
    .table td {
        padding: 16px 12px !important;
        color: #334155;
    }

    /* Badge Makronutrisi Gizi */
    .badge-kalori {
        background: rgba(245, 158, 11, 0.1) !important;
        color: #d97706 !important;
        border: 1px solid rgba(245, 158, 11, 0.2);
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
    }
    .badge-protein {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #2563eb !important;
        border: 1px solid rgba(59, 130, 246, 0.2);
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
    }

    /* Status Badge */
    .badge-status-aktif {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #059669 !important;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .badge-status-nonaktif {
        background: rgba(100, 116, 139, 0.15) !important;
        color: #475569 !important;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* Desain Input & Jendela Modal */
    .modal-content {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 20px 50px rgba(6, 28, 29, 0.15) !important;
    }
    .modal-header {
        background: #061c1d !important;
        color: #ffffff !important;
        border-bottom: none !important;
        padding: 20px 24px !important;
    }
    .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }
    .form-control, .form-select {
        border-radius: 10px !important;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus, .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
</style>

<div class="container-fluid pt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mbg-title mb-1">Katalog Gizi Menu Makanan</h3>
            <p class="text-muted small mb-0">Standardisasi takaran makronutrisi porsi makanan bergizi gratis untuk anak didik.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-utensils me-2"></i> Tambah Menu Baru
        </button>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-heart-pulse text-success me-2"></i> Komposisi Nutrisi & Sajian Sehat
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Kombinasi Paket Menu</th>
                        <th class="text-center">Energi (Kalori)</th>
                        <th class="text-center">Kadar Protein</th>
                        <th class="text-center">Status Distribusi</th>
                        <th class="text-center pe-4">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($menus) > 0): ?>
                        <?php foreach($menus as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #061c1d;">
                                <i class="fas fa-bowl-food text-secondary opacity-50 me-2"></i><?= htmlspecialchars($row['nama_menu']); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge-kalori font-monospace">
                                    <i class="fas fa-bolt me-1"></i> <?= $row['kalori']; ?> Kcal
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-protein font-monospace">
                                    <i class="fas fa-egg me-1"></i> <?= $row['protein']; ?> gram
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="<?= $row['status'] == 'aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif'; ?>">
                                    <i class="fas <?= $row['status'] == 'aktif' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                    <?= strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light border text-warning px-3 rounded-3 me-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_menu']; ?>">
                                    <i class="fas fa-pen-to-square me-1"></i> Edit
                                </button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Hapus menu ini? Semua resep komposisi terlampir akan terhapus!');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_menu" value="<?= $row['id_menu']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-3 rounded-3">
                                        <i class="fas fa-trash-can me-1"></i> Hapus
                                    </button>
                                </form>

                                <div class="modal fade" id="modalEdit<?= $row['id_menu']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="" class="modal-content text-start">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id_menu" value="<?= $row['id_menu']; ?>">
                                            
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="fas fa-honey-pot me-2 text-warning"></i> Edit Kandungan Gizi & Menu
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Nama Kombinasi Paket Menu</label>
                                                    <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($row['nama_menu']); ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Kadar Energi (Kalori)</label>
                                                        <div class="input-group">
                                                            <input type="number" name="kalori" class="form-control font-monospace" value="<?= $row['kalori']; ?>" required>
                                                            <span class="input-group-text bg-light text-muted small">Kcal</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Kadar Protein</label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="protein" class="form-control font-monospace" value="<?= $row['protein']; ?>" required>
                                                            <span class="input-group-text bg-light text-muted small">gram</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Status Operasional Menu</label>
                                                    <select name="status" class="form-select">
                                                        <option value="aktif" <?= $row['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif (Dapat Didistribusikan)</option>
                                                        <option value="nonaktif" <?= $row['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                                                    </select>
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
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-kitchen-set d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada formulasi daftar menu sehat yang tersimpan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-2 text-success"></i> Registrasi Formulasi Menu Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Kombinasi Komponen Menu</label>
                    <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Nasi Gurih + Ayam Fillet Bakar + Sup Brokoli" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Kandungan Kalori</label>
                        <div class="input-group">
                            <input type="number" name="kalori" class="form-control font-monospace" placeholder="0" required>
                            <span class="input-group-text bg-light text-muted small">Kcal</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Kandungan Protein</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="protein" class="form-control font-monospace" placeholder="0.00" required>
                            <span class="input-group-text bg-light text-muted small">Gram</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Status Awal Menu</label>
                    <select name="status" class="form-select">
                        <option value="aktif">Aktif (Dapat Segera Digunakan)</option>
                        <option value="nonaktif">Nonaktif (Arsip Dulu)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Simpan Data Menu</button>
            </div>
        </form>
    </div>
</div>