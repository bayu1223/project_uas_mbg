<?php
// /users/index.php
require_once '../config/database.php';
require_once '../includes/header.php';

// Mengambil seluruh data user/admin
$stmt = $pdo->query("SELECT id_users, nama, username, role, created_at FROM users ORDER BY id_users DESC");
$users = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Manajemen Kelola Admin & Petugas</h4>
    <a href="create.php" class="btn btn-primary btn-sm">Tambah Pengguna Baru</a>
</div>

<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        if ($_GET['status'] === 'success') echo "Pengguna baru berhasil ditambahkan!";
        elseif ($_GET['status'] === 'updated') echo "Data pengguna berhasil diperbarui!";
        elseif ($_GET['status'] === 'deleted') echo "Pengguna berhasil dihapus dari sistem!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role / Otoritas</th>
                        <th>Tanggal Registrasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data pengguna.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td>
                                    <?php if ($row['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">Petugas</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['created_at'] ?></td>
                                <td class="text-center">
                                    <a href="update.php?id=<?= $row['id_users'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $row['id_users'] ?>">Hapus</button>
                                </td>
                            </tr>

                            <div class="modal fade" id="deleteUserModal<?= $row['id_users'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Apakah Anda yakin ingin menghapus akun milik <strong><?= htmlspecialchars($row['nama']) ?></strong>?<br>
                                            <small class="text-danger">*Tindakan ini tidak dapat dibatalkan.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <a href="delete.php?id=<?= $row['id_users'] ?>" class="btn btn-danger btn-sm">Hapus Akun</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>