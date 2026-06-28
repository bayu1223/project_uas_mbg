<?php
require_once __DIR__ . '/../controllers/SupplierController.php';
$supCtrl = new SupplierController();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $supCtrl->create(Database::sanitize($_POST['nama_supplier']), Database::sanitize($_POST['alamat']), Database::sanitize($_POST['no_telp']), Database::sanitize($_POST['email']));
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['id_supplier']) && !empty($_POST['id_supplier'])) {
            $res = $supCtrl->update($_POST['id_supplier'], Database::sanitize($_POST['nama_supplier']), Database::sanitize($_POST['alamat']), Database::sanitize($_POST['no_telp']), Database::sanitize($_POST['email']));
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3'>Gagal memperbarui: ID Supplier tidak valid.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $res = $supCtrl->delete($_POST['id_supplier']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$suppliers = $supCtrl->readAll();
?>

<!-- CUSTOM STYLING SINKRONISASI THEME MBG -->
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
    .form-control {
        border-radius: 10px !important;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
</style>

<div class="container-fluid pt-2">
    <!-- Header Atas -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mbg-title mb-1">Data Master Supplier / Vendor</h3>
            <p class="text-muted small mb-0">Kelola daftar mitra penyedia pasokan bahan makanan bergizi gratis.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Supplier
        </button>
    </div>

    <!-- Notifikasi Sistem -->
    <?= $msg; ?>

    <!-- Wrapper Tabel -->
    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-handholding-hand me-2 text-success"></i> Daftar Kontrak Mitra Aktif
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Supplier</th>
                        <th>Alamat Distribusi</th>
                        <th class="text-center">No. Telepon</th>
                        <th>Email Resmi</th>
                        <th class="text-center pe-4">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($suppliers) > 0): ?>
                        <?php foreach($suppliers as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #061c1d;">
                                <i class="fas fa-building text-secondary opacity-50 me-2"></i><?=($row['nama_supplier']); ?>
                            </td>
                            <td class="text-muted small" style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                <?= htmlspecialchars($row['alamat']); ?>
                            </td>
                            <td class="text-center font-monospace small"><?= htmlspecialchars($row['no_telp']); ?></td>
                            <td><span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-3 font-monospace"><?= htmlspecialchars($row['email']); ?></span></td>
                            <td class="text-center pe-4">
                                <!-- Tombol Aksi Bergaya Minimalis -->
                                <button class="btn btn-sm btn-light border text-warning px-3 rounded-3 me-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_supplier']; ?>">
                                    <i class="fas fa-pen-to-square me-1"></i> Edit
                                </button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Hapus supplier ini? Semua data stok terkait akan ikut terhapus!');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_supplier" value="<?= $row['id_supplier']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-3 rounded-3">
                                        <i class="fas fa-trash-can me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- MODAL EDIT DATA SUPPLIER -->
                        <tr>
                            <td colspan="5" class="p-0 border-0">
                                <div class="modal fade" id="modalEdit<?= $row['id_supplier']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['id_supplier']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="" class="modal-content text-start">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id_supplier" value="<?= $row['id_supplier']; ?>">
                                            
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold" id="modalEditLabel<?= $row['id_supplier']; ?>">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Perbarui Profil Vendor
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Nama Supplier / Perusahaan</label>
                                                    <input type="text" name="nama_supplier" class="form-control" value="<?=($row['nama_supplier']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Alamat Kantor/Gudang</label>
                                                    <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($row['alamat']); ?></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                                                        <input type="text" name="no_telp" class="form-control font-monospace" value="<?= htmlspecialchars($row['no_telp']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Alamat Email Korespondensi</label>
                                                        <input type="email" name="email" class="form-control font-monospace" value="<?= htmlspecialchars($row['email']); ?>" required>
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
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada data supplier yang terdaftar di sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SUPPLIER BARU -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalTambahLabel">
                    <i class="fas fa-plus-circle me-2 text-success"></i> Tambah Mitra Supplier Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Supplier / Perusahaan</label>
                    <input type="text" name="nama_supplier" class="form-control" placeholder="Contoh: PT. Sumber Pangan Makmur" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Alamat Operasional Kontrak</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Tuliskan alamat lengkap kantor atau pusat distribusi..." required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">No. Telepon Aktif</label>
                        <input type="text" name="no_telp" class="form-control font-monospace" placeholder="08XXXXXXXXXX" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Email Perusahaan</label>
                        <input type="email" name="email" class="form-control font-monospace" placeholder="vendor@email.com" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Simpan Data Mitra</button>
            </div>
        </form>
    </div>
</div>