<?php
require_once __DIR__ . '/../controllers/UserController.php';
$userCtrl = new UserController();
$msg = '';

// Proses Penanganan Request Aksi (Tambah / Edit / Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $res = $userCtrl->create(
            Database::sanitize($_POST['nama']),
            Database::sanitize($_POST['username']),
            $_POST['password'],
            $_POST['role']
        );
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['id_users']) && !empty($_POST['id_users'])) {
            $res = $userCtrl->update(
                $_POST['id_users'],
                Database::sanitize($_POST['nama']),
                Database::sanitize($_POST['username']),
                $_POST['password'],
                $_POST['role']
            );
            $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3'>Gagal memperbarui: ID Pengguna tidak valid.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $res = $userCtrl->delete($_POST['id_users'], $_SESSION['user_id']);
        $msg = "<div class='alert alert-{$res['status']} alert-dismissible fade show border-0 shadow-sm rounded-3'>{$res['msg']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

$all_users = $userCtrl->readAll();
?>

<style>
    .mbg-title {
        color: #061c1d;
        font-weight: 700;
    }
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
    .badge-role-admin {
        background: rgba(6, 28, 29, 0.9) !important;
        color: #ffffff !important;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .badge-role-petugas {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #059669 !important;
        border: 1px solid rgba(16, 185, 129, 0.2);
        font-weight: 600;
        padding: 5px 13px;
        border-radius: 8px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
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
            <h3 class="mbg-title mb-1">Manajemen Hak Akses & Pengguna</h3>
            <p class="text-muted small mb-0">Kelola kredensial akun login, batasan hak akses, dan privilese operasional sistem.</p>
        </div>
        <button class="btn btn-mbg-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="fas fa-user-plus me-2"></i> Tambah User Baru
        </button>
    </div>

    <?= $msg; ?>

    <div class="card mbg-table-card border-0 mb-4">
        <div class="mbg-table-header d-flex align-items-center">
            <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                <i class="fas fa-users text-success me-2"></i> Daftar Karyawan & Pengguna Sistem
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 text-center" style="width: 80px;">ID</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th class="text-center">Role Privilese</th>
                        <th>Tanggal Registrasi</th>
                        <th class="text-center pe-4">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_users) > 0): ?>
                        <?php foreach($all_users as $user): ?>
                        <tr>
                            <td class="text-center font-monospace small text-secondary ps-4"><?= $user['id_users']; ?></td>
                            <td class="fw-bold" style="color: #061c1d;">
                                <i class="far fa-id-card text-secondary opacity-50 me-2"></i><?= htmlspecialchars($user['nama']); ?>
                            </td>
                            <td><code class="text-success bg-success bg-opacity-10 px-2 py-1 rounded font-monospace"><?= htmlspecialchars($user['username']); ?></code></td>
                            <td class="text-center">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge-role-admin text-uppercase"><i class="fas fa-user-shield me-1"></i> <?= $user['role']; ?></span>
                                <?php else: ?>
                                    <span class="badge-role-petugas text-uppercase"><i class="fas fa-user-gear me-1"></i> <?= $user['role']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-secondary font-monospace">
                                <i class="far fa-clock me-1"></i><?= date('d M Y, H:i', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light border px-3 rounded-3 me-1 text-secondary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditUser<?= $user['id_users']; ?>">
                                    <i class="fas fa-user-pen me-1 text-warning"></i> Ubah
                                </button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_users" value="<?= $user['id_users']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-3 rounded-3">
                                        <i class="fas fa-user-minus me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-users-slash d-block fs-2 mb-3 text-opacity-25 text-secondary"></i>
                                Belum ada data pengguna / staf operator yang terdaftar di database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="add">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-user-plus me-2 text-success"></i> Registrasi Akun Pengguna Baru
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama staf atau personel" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username Identitas</label>
                    <input type="text" name="username" class="form-control font-monospace" placeholder="Contoh: operator_dapur" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Kata Sandi (Password)</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat kata sandi akun" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Otoritas Peran (Role Lapangan)</label>
                    <select name="role" class="form-select" required>
                        <option value="petugas" selected>Petugas (Hanya Entri Transaksi & Distribusi)</option>
                        <option value="admin">Admin (Akses Modul & Pengaturan Keseluruhan)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-mbg-primary px-4">Daftarkan Akun</button>
            </div>
        </form>
    </div>
</div>

<?php if (count($all_users) > 0): ?>
    <?php foreach($all_users as $user): ?>
    <div class="modal fade" id="modalEditUser<?= $user['id_users']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="" class="modal-content text-start">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_users" value="<?= $user['id_users']; ?>">
                
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">
                        <i class="fas fa-user-slider me-2 text-success"></i> Modifikasi Otoritas Pengguna
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username Akun</label>
                        <input type="text" name="username" class="form-control font-monospace" value="<?= htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                        <div class="form-text text-muted small" style="font-size: 0.75rem;">*Isi kolom ini hanya jika karyawan lupa atau ingin mereset kata sandi lama mereka.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tingkat Hak Akses (Role)</label>
                        <select name="role" class="form-select" required>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin (Akses Penuh Master)</option>
                            <option value="petugas" <?= $user['role'] === 'petugas' ? 'selected' : ''; ?>>Petugas (Akses Logistik & Distribusi)</option>
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
    <?php endforeach; ?>
<?php endif; ?>