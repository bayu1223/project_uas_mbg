<?php
// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'templates/header.php';
require_once 'templates/sidebar.php';
require_once 'config/Database.php';

$page = $_GET['page'] ?? 'dashboard';
$role = $_SESSION['role'] ?? 'petugas'; 

// Sinkronisasi variabel agar terbaca sebagai proteksi di file-file sub-view
$user_role = $role; 

// Daftar halaman yang HARUS diakses oleh Admin saja (Akses Menu Utama)
$admin_pages = ['supplier', 'stok_bahan', 'sekolah', 'menu', 'detail_menu', 'users'];

// JIKA halaman yang diminta masuk dalam daftar halaman khusus admin, 
// DAN role user saat ini BUKAN admin, maka blokir otomatis!
if (in_array($page, $admin_pages) && $role !== 'admin') {
    echo "
    <div class='container mt-5'>
        <div class='alert alert-danger border-0 shadow-sm p-4 text-center'>
            <h4 class='alert-heading fw-bold'>⚠️ Akses Ditolak!</h4>
            <p class='mb-0 text-muted'>Maaf, akun Anda dengan peran <strong>".strtoupper($role)."</strong> tidak memiliki izin otoritas untuk membuka modul <strong>$page</strong>.</p>
            <hr>
            <a href='index.php' class='btn btn-outline-danger btn-sm mt-2'>Kembali ke Dashboard</a>
        </div>
    </div>";
    
    require_once 'templates/footer.php';
    exit(); 
}

// Router Internal Core View
if ($page === 'dashboard') {
    require_once 'controllers/DashboardController.php';
    $dashCtrl = new DashboardController();
    $data = $dashCtrl->getSummary();
    ?>
    
    <style>
        .dashboard-title { color: #061c1d; font-weight: 700; letter-spacing: -0.5px; }
        .mbg-card { border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.8) !important; background: #ffffff !important; box-shadow: 0 10px 25px rgba(6, 28, 29, 0.03) !important; transition: transform 0.3s ease, box-shadow 0.3s ease; overflow: hidden; position: relative; }
        .mbg-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(6, 28, 29, 0.08) !important; }
        .mbg-card-primary::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: #10b981; }
        .mbg-card-success::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: #059669; }
        .mbg-card-warning::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: #f59e0b; }
        .card-icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .mbg-table-container { border-radius: 16px !important; border: 1px solid rgba(0, 0, 0, 0.03) !important; box-shadow: 0 10px 30px rgba(6, 28, 29, 0.02) !important; background: #ffffff; }
        .mbg-table-header { background-color: #ffffff !important; border-bottom: 2px solid #f1f5f9 !important; color: #061c1d !important; font-weight: 700; }
        .table th { font-size: 0.8rem; text-uppercase: true; letter-spacing: 0.5px; color: #64748b; padding: 16px 12px !important; }
        .table td { padding: 16px 12px !important; color: #334155; }
        .badge-masuk { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.2); padding: 6px 12px !important; border-radius: 8px; }
        .badge-keluar { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.2); padding: 6px 12px !important; border-radius: 8px; }
    </style>

    <div class="container-fluid pt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="dashboard-title mb-1">Ringkasan Data MBG</h3>
                <p class="text-muted small mb-0">Memantau pasokan gizi, logistik, dan sebaran sekolah secara berkala.</p>
            </div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm small font-monospace">
                <i class="far fa-calendar-alt me-1 text-success"></i> <?= date('d M Y'); ?>
            </span>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card mbg-card mbg-card-primary p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Institusi Sekolah</span>
                            <h2 class="fw-bold mt-2 mb-0" style="color: #061c1d;"><?= $data['total_sekolah']; ?> <span class="fs-5 fw-normal text-muted">Sekolah</span></h2>
                            <?php if ($role === 'admin'): ?>
                                <a href="index.php?page=sekolah" class="small text-success text-decoration-none d-block mt-2">Kelola Data →</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mbg-card mbg-card-success p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Mitra Penyuplai</span>
                            <h2 class="fw-bold mt-2 mb-0" style="color: #061c1d;"><?= $data['total_supplier']; ?> <span class="fs-5 fw-normal text-muted">Vendor</span></h2>
                            <?php if ($role === 'admin'): ?>
                                <a href="index.php?page=supplier" class="small text-success text-decoration-none d-block mt-2">Kelola Mitra →</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-icon-box" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                            <i class="fas fa-handholding-hand"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mbg-card mbg-card-warning p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Variasi Menu Aktif</span>
                            <h2 class="fw-bold mt-2 mb-0" style="color: #061c1d;"><?= $data['total_menu']; ?> <span class="fs-5 fw-normal text-muted">Paket</span></h2>
                            <?php if ($role === 'admin'): ?>
                                <a href="index.php?page=menu" class="small text-warning text-decoration-none d-block mt-2">Formulasi Gizi →</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fas fa-bowl-food"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mbg-table-container border-0 overflow-hidden">
            <div class="card-header mbg-table-header py-3.5 px-4 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-uppercase tracking-wide" style="font-size: 0.85rem; color: #061c1d;">
                    <i class="fas fa-history me-2 text-success"></i> Log Transaksi Stok Terakhir
                </span>
                <span class="badge bg-light text-secondary rounded-pill px-3 py-1.5 fw-normal">Realtime Update</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>Bahan Baku</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="pe-4">Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['recent_logs'])): ?>
                            <?php foreach($data['recent_logs'] as $log): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><i class="far fa-clock me-1 text-opacity-50"></i> <?= $log['tanggal_transaksi']; ?></td>
                                <td class="fw-semibold" style="color: #061c1d;"><?= htmlspecialchars($log['nama_bahan']); ?></td>
                                <td class="text-center">
                                    <span class="<?= $log['jenis_transaksi'] == 'masuk' ? 'badge-masuk' : 'badge-keluar'; ?> fw-semibold small text-uppercase">
                                        <?= $log['jenis_transaksi'] == 'masuk' ? '<i class="fas fa-arrow-down me-1"></i> Masuk' : '<i class="fas fa-arrow-up me-1"></i> Keluar'; ?>
                                    </span>
                                </td>
                                <td class="text-center font-monospace fw-bold text-secondary"><?= number_format($log['jumlah']); ?></td>
                                <td class="pe-4"><span class="badge bg-light text-dark border px-2.5 py-1.5 rounded"><?= htmlspecialchars($log['nama_user']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Belum ada riwayat transaksi logistik hari ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // Memuat File View CRUD yang Dituju secara Dinamis
    $viewPath = __DIR__ . "/views/" . $page . ".php";
    if (file_exists($viewPath)) {
        
        // 🛑 PROTEKSI UNTUK PETUGAS (DENGAN WHITELIST HALAMAN DISTRIBUSI)
        if ($role !== 'admin') {
            
            // Cek jika halaman saat ini BUKAN halaman 'distribusi'
            if ($page !== 'distribusi') {
                // Jalankan proteksi ketat untuk halaman umum lainnya
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo "<div class='container mt-3'><div class='alert alert-danger shadow-sm border-0'>⚠️ <strong>Akses Ditolak:</strong> Anda tidak diizinkan memodifikasi atau menambah data pada modul ini.</div></div>";
                    require_once 'templates/footer.php';
                    exit();
                }
                
                // Rekam output HTML halaman view sebelum dikirim ke browser untuk disaring
                ob_start();
                require_once $viewPath;
                $html_output = ob_get_clean();
                
                // Menghapus elemen Aksi secara paksa (Hanya untuk halaman non-whitelist)
                $html_output = preg_replace('/<button[^>]*data-bs-toggle=["\']modal["\'][^>]*>.*?<\/button>/is', '', $html_output);
                $html_output = preg_replace('/<form[^>]*method=["\']POST["\'][^>]*>.*?<\/form>/is', '', $html_output);
                $html_output = preg_replace('/<th[^>]*>Aksi<\/th>/i', '', $html_output);
                $html_output = preg_replace('/<td[^>]*>.*?btn-danger.*?<\/td>/is', '', $html_output);
                $html_output = preg_replace('/<td[^>]*>.*?btn-warning.*?<\/td>/is', '', $html_output);
                
                echo $html_output;
            } else {
                // Khusus halaman 'distribusi', muat apa adanya agar tombol Selesai (POST) milik petugas bekerja
                require_once $viewPath;
            }
            
        } else {
            // Jika Admin, muat file secara normal dengan hak akses penuh
            require_once $viewPath;
        }
        
    } else {
        echo "
        <div class='container-fluid pt-4'>
            <div class='alert alert-warning border-0 shadow-sm' style='border-radius: 12px;'>
                <i class='fas fa-search me-2'></i> Modul atau halaman tidak ditemukan!
            </div>
        </div>";
    }
}

require_once 'templates/footer.php';
?>