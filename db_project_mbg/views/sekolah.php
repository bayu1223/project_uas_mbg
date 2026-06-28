<?php
require_once __DIR__ . '/../controllers/SekolahController.php';
$sekCtrl = new SekolahController();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $sekCtrl->create(Database::sanitize($_POST['nama_sekolah']), Database::sanitize($_POST['alamat']), Database::sanitize($_POST['kepala_sekolah']), $_POST['jumlah_siswa'], Database::sanitize($_POST['no_telp']));
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['id_sekolah']) && !empty($_POST['id_sekolah'])) {
            $res = $sekCtrl->update($_POST['id_sekolah'], Database::sanitize($_POST['nama_sekolah']), Database::sanitize($_POST['alamat']), Database::sanitize($_POST['kepala_sekolah']), $_POST['jumlah_siswa'], Database::sanitize($_POST['no_telp']));
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3'>Gagal memperbarui: ID Sekolah tidak valid.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $res = $sekCtrl->delete($_POST['id_sekolah']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$sekolah_list = $sekCtrl->readAll();
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

    /* Badge Khusus Alokasi Porsi Pangan */
    .badge-porsi {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #059669 !important;
        border: 1px solid rgba(16, 185, 129, 0.2);
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mbg-title mb-1">Data Master Wilayah Sekolah</h3>
            <p class="text-muted small mb-0">Kelola daftar instansi satuan pendidikan penerima manfaat Makanan Bergizi Gratis.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-school me-2"></i> Registrasi Sekolah
        </button>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-graduation-cap me-2 text-success"></i> Entitas Lembaga Pendidikan Mitra
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Sekolah</th>
                        <th>Kepala Sekolah</th>
                        <th class="text-center">Jumlah Siswa (Porsi)</th>
                        <th class="text-center">No. Kontak</th>
                        <th>Alamat Lengkap</th>
                        <th class="text-center pe-4">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sekolah_list) > 0): ?>
                        <?php foreach($sekolah_list as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #061c1d;">
                            <?= htmlspecialchars($row['nama_sekolah']); ?>
                            </td>
                            <td class="fw-medium text-secondary">
                                <?=($row['kepala_sekolah']); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge-porsi font-monospace">
                                    <i class="fas fa-user-graduate me-1"></i> <?= $row['jumlah_siswa']; ?> Anak
                                </span>
                            </td>
                            <td class="text-center font-monospace small"><?= htmlspecialchars($row['no_telp']); ?></td>
                            <td class="text-muted small" style="max-width: 260px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                <?= htmlspecialchars($row['alamat']); ?>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light border text-warning px-3 rounded-3 me-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_sekolah']; ?>">
                                    <i class="fas fa-pen-to-square me-1"></i> Edit
                                </button>
                        
                                <form method="POST" action="" onsubmit="return confirm('Hapus data sekolah ini? Semua rekam log distribusi terkait akan dihapus!');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_sekolah" value="<?= $row['id_sekolah']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-3 rounded-3">
                                        <i class="fas fa-trash-can me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>                       

                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <div class="modal fade" id="modalEdit<?= $row['id_sekolah']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['id_sekolah']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="" class="modal-content text-start">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id_sekolah" value="<?= $row['id_sekolah']; ?>">
                                            
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold" id="modalEditLabel<?= $row['id_sekolah']; ?>">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Perbarui Data Lembaga
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Nama Instansi Sekolah</label>
                                                    <input type="text" name="nama_sekolah" class="form-control" value="<?= htmlspecialchars($row['nama_sekolah']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Nama Kepala Sekolah</label>
                                                    <input type="text" name="kepala_sekolah" class="form-control" value="<?= htmlspecialchars($row['kepala_sekolah']); ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Jumlah Siswa Aktif (Porsi)</label>
                                                        <input type="number" name="jumlah_siswa" class="form-control font-monospace" value="<?= $row['jumlah_siswa']; ?>" min="1" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold text-secondary">No. Telepon Instansi</label>
                                                        <input type="text" name="no_telp" class="form-control font-monospace" value="<?= htmlspecialchars($row['no_telp']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary">Alamat Terinci Wilayah</label>
                                                    <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($row['alamat']); ?></textarea>
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
                                <i class="fas fa-school-flag d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada data sekolah mitra yang terdaftar di dalam sistem.
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
                    <i class="fas fa-plus-circle me-2 text-success"></i> Registrasi Sekolah Mitra Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Instansi Sekolah</label>
                    <input type="text" name="nama_sekolah" class="form-control" placeholder="Contoh: SDN 01 Pemuda Bangsa" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" class="form-control" placeholder="Tuliskan nama lengkap beserta gelar..." required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah Siswa Aktif</label>
                        <input type="number" name="jumlah_siswa" class="form-control font-monospace" placeholder="0" min="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">No. Telepon Instansi</label>
                        <input type="text" name="no_telp" class="form-control font-monospace" placeholder="Contoh: (021) XXXXXX / 08XX" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Alamat Lengkap Operasional</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Tuliskan nama jalan, RT/RW, Kecamatan, dan Kabupaten/Kota..." required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Simpan Data Sekolah</button>
            </div>
        </form>
    </div>
</div>