<?php
require_once __DIR__ . '/../controllers/StokBahanController.php';
$stokCtrl = new StokBahanController();
$msg = '';

// Proses penanganan aksi formulir (Tambah / Edit / Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $stokCtrl->create(
            Database::sanitize($_POST['nama_bahan']),
            $_POST['jumlah'],
            Database::sanitize($_POST['satuan']),
            $_POST['id_supplier']
        );
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['id_stok']) && !empty($_POST['id_stok'])) {
            $res = $stokCtrl->update(
                $_POST['id_stok'],
                Database::sanitize($_POST['nama_bahan']),
                $_POST['jumlah'],
                Database::sanitize($_POST['satuan']),
                $_POST['id_supplier']
            );
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3'>Gagal memperbarui: ID stok tidak valid.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $res = $stokCtrl->delete($_POST['id_stok']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Mengambil data utama komponen stok bahan
$stok_list = $stokCtrl->readAll();

// Mengambil data master supplier untuk keperluan komponen dropdown (<select>)
$database = new Database();
$dbConn = $database->getConnection();
$supplier_list = $dbConn->query("SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC")->fetchAll(PDO::FETCH_ASSOC);
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

    /* Badge Volume Ketersediaan Pangan */
    .badge-stok-aman {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 6px 14px !important;
        border-radius: 8px;
        font-weight: 700;
    }
    .badge-stok-kritis {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.2);
        padding: 6px 14px !important;
        border-radius: 8px;
        font-weight: 700;
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
            <h3 class="mbg-title mb-1">Data Master Stok Bahan Baku</h3>
            <p class="text-muted small mb-0">Monitor ketersediaan volume logistik dapur produksi makanan bergizi gratis.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Bahan Baku
        </button>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-warehouse me-2 text-success"></i> Inventori Komoditas Bahan Baku
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Bahan</th>
                        <th class="text-center">Volume Terkini</th>
                        <th class="text-center">Satuan Ukur</th>
                        <th>Mitra Supplier / Vendor</th>
                        <th>Pembaruan Terakhir</th>
                        <th class="text-center pe-4">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($stok_list) > 0): ?>
                        <?php foreach($stok_list as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #061c1d;">
                                <i class="fas fa-cubes text-secondary opacity-50 me-2"></i><?= htmlspecialchars($row['nama_bahan']); ?>
                            </td>
                            <td class="text-center">
                                <span class="<?= $row['jumlah'] > 10 ? 'badge-stok-aman' : 'badge-stok-kritis'; ?> font-monospace">
                                    <?= $row['jumlah'] > 10 ? '<i class="fas fa-check-circle me-1"></i>' : '<i class="fas fa-triangle-exclamation me-1"></i>'; ?>
                                    <?= $row['jumlah']; ?>
                                </span>
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-3 text-lowercase fw-normal"><?= htmlspecialchars($row['satuan']); ?></span></td>
                            <td>
                                <span class="fw-semibold">
                                    <?= $row['nama_supplier'] ? '<i class="fas fa-building text-opacity-50 text-secondary me-2"></i>' .($row['nama_supplier']) : '<em class="text-muted small"><i class="fas fa-circle-minus me-1"></i> Tidak ada vendor</em>'; ?>
                                </span>
                            </td>
                            <td class="text-muted small"><i class="far fa-clock me-1 text-opacity-50"></i> <?= $row['tanggal_update']; ?></td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light border text-warning px-3 rounded-3 me-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_stok']; ?>">
                                    <i class="fas fa-pen-to-square me-1"></i> Edit
                                </button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Hapus bahan baku ini? Seluruh log riwayat transaksi mutasi terkait bahan ini akan ikut terhapus!');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_stok" value="<?= $row['id_stok']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-3 rounded-3">
                                        <i class="fas fa-trash-can me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <div class="modal fade" id="modalEdit<?= $row['id_stok']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['id_stok']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="" class="modal-content text-start">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id_stok" value="<?= $row['id_stok']; ?>">
                                            
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold" id="modalEditLabel<?= $row['id_stok']; ?>">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Modifikasi Komoditas Pangan
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Nama Bahan Baku</label>
                                                    <input type="text" name="nama_bahan" class="form-control" value="<?= htmlspecialchars($row['nama_bahan']); ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Jumlah / Volume Fisik</label>
                                                        <input type="number" step="0.01" name="jumlah" class="form-control font-monospace" value="<?= $row['jumlah']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Satuan Ukuran</label>
                                                        <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($row['satuan']); ?>" placeholder="Contoh: kg, liter" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Pilih Penyuplai Kemitraan (Vendor)</label>
                                                    <select name="id_supplier" class="form-select" required>
                                                        <option value="">-- Pilih Vendor Mitra --</option>
                                                        <?php foreach($supplier_list as $sup): ?>
                                                            <option value="<?= $sup['id_supplier']; ?>" <?= $sup['id_supplier'] == $row['id_supplier'] ? 'selected' : ''; ?>>
                                                                <?=($sup['nama_supplier']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
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
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-box-open d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada data stok bahan baku yang tersimpan di sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalTambahLabel">
                    <i class="fas fa-plus-circle me-2 text-success"></i> Registrasi Komoditas Logistik Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Bahan Baku</label>
                    <input type="text" name="nama_bahan" class="form-control" placeholder="Contoh: Beras Ramos SLY, Telur Ayam Omega" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah Saldo Awal</label>
                        <input type="number" step="1" name="jumlah" class="form-control font-monospace" value="0" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Satuan Ukuran</label>
                        <input type="text" name="satuan" class="form-control" placeholder="Contoh: kg, liter, papan" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Pilih Penyuplai Kemitraan (Vendor)</label>
                    <select name="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Vendor Mitra --</option>
                        <?php foreach($supplier_list as $sup): ?>
                            <option value="<?= $sup['id_supplier']; ?>"><?=($sup['nama_supplier']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Simpan Data Pangan</button>
            </div>
        </form>
    </div>
</div>