<?php
$role = $_SESSION['role'] ?? 'petugas';
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<style>
    /* Mengatur sidebar agar menyesuaikan tinggi layar penuh pada desktop */
    @media (min-width: 768px) {
        .mbg-sidebar {
            height: 100vh; /* Mengunci tinggi tepat seukuran layar */
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column; /* Membagi header dan list menu secara vertikal */
        }
    }
    
    .mbg-sidebar {
        background-color: #061c1d !important; /* Deep Emerald Night */
        box-shadow: 4px 0 20px rgba(6, 28, 29, 0.08);
        border-right: 1px solid rgba(255, 255, 255, 0.04);
    }
    
    .mbg-sidebar-brand {
        background: linear-gradient(180deg, #082728 0%, #061c1d 100%);
        padding: 24px 16px;
        flex-shrink: 0; /* Mencegah header mengecil saat di-scroll */
    }

    /* FITUR SCROLLBAR INDEPENDEN */
    .mbg-sidebar .menu-scroll-container {
        overflow-y: auto; /* Memunculkan scrollbar vertikal hanya jika menu meluap */
        flex-grow: 1;    /* Mengambil sisa ruang tinggi yang tersedia */
        height: calc(100vh - 110px); /* Cadangan fallback tinggi untuk mobile offcanvas */
    }

    /* Kustomisasi Scrollbar Halus Khusus Area Sidebar */
    .mbg-sidebar .menu-scroll-container::-webkit-scrollbar {
        width: 5px;
    }
    .mbg-sidebar .menu-scroll-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.01);
    }
    .mbg-sidebar .menu-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .mbg-sidebar .menu-scroll-container::-webkit-scrollbar-thumb:hover {
        background: rgba(16, 185, 129, 0.4); /* Berubah emerald saat diarahkan kursor */
    }

    .mbg-sidebar .nav-header {
        font-size: 0.72rem;
        letter-spacing: 1.2px;
        color: #648182 !important;
        font-weight: 700;
        padding: 15px 18px 8px 18px;
    }
    .mbg-sidebar .list-group-item {
        background: transparent !important;
        color: #9cb0b1 !important;
        font-size: 0.9rem;
        font-weight: 400;
        border: none !important;
        padding: 8px 18px;
        margin: 4px 15px;
        border-radius: 10px !important;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    .mbg-sidebar .list-group-item i {
        font-size: 1.05rem;
        width: 28px;
        transition: transform 0.2s ease;
    }
    /* Hover State */
    .mbg-sidebar .list-group-item:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.05) !important;
        width: 90%;
    }
    .mbg-sidebar .list-group-item:hover i {
        transform: scale(1.1);
    }
    /* Active State (Sesuai Halaman Terbuka) */
    .mbg-sidebar .list-group-item.active-mbg {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        width: 90%;
    }
    .mbg-sidebar .list-group-item.active-mbg i {
        color: #ffffff !important;
    }
    
    /* Tombol Keluar */
    .mbg-sidebar .list-group-item.btn-logout {
        background: #ef4444 !important;
        color: #ffffff !important;
        margin-top: 30px !important;
        border: 1px solid #ef4444 !important;
        width: 90%;
        margin-bottom: 20px; /* Jarak aman di bagian bawah setelah scroll mentok */
    }
    .mbg-sidebar .list-group-item.btn-logout:hover {
        background: #dc2626 !important;
        color: #ffffff !important;
    }
    .mbg-sidebar .list-group-item.btn-logout i {
        color: #ffffff !important;
    }

    /* Navbar khusus mobile */
    .mbg-mobile-nav {
        background-color: #061c1d;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>

<nav class="navbar d-md-none mbg-mobile-nav px-3 sticky-top">
    <span class="navbar-brand text-white fw-bold mb-0" style="font-size: 1rem;">
        <i class="fas fa-solid fa-utensils me-1"></i> MBG SYSTEM
    </span>
    <button class="btn text-white border-secondary border-opacity-25" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<div class="offcanvas-md offcanvas-start col-md-3 col-lg-2 px-0 mbg-sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <div class="mbg-sidebar-brand text-center border-bottom border-secondary border-opacity-10 d-flex flex-column align-items-center position-relative">
        <button type="button" class="btn-close btn-close-white d-md-none position-absolute top-50 end-0 translate-middle-y me-3" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        
        <h5 class="text-white fw-bold mb-1 tracking-wide" style="font-size: 1.1rem;">
            <i class="fas fa-solid fa-utensils me-1"></i> MBG SYSTEM
        </h5>
        <span class="badge bg-warning bg-opacity-15 text-success border border-success border-opacity-25 px-2.5 py-1" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">
            <i class="fas fa-shield-halved me-1 small"></i> <?= strtoupper($role); ?>
        </span>
    </div>
    
    <div class="menu-scroll-container">
        <div class="list-group list-group-flush p-2">
            
            <div class="nav-header text-uppercase">Utama</div>
            <a href="index.php" class="list-group-item list-group-item-action <?= $current_page === 'dashboard' ? 'active-mbg' : ''; ?>">
                <i class="fas fa-chart-pie me-2"></i> Dashboard
            </a>

            <?php if ($role === 'admin'): ?>
                <div class="nav-header text-uppercase">Data Master</div>
                <a href="index.php?page=supplier" class="list-group-item list-group-item-action <?= $current_page === 'supplier' ? 'active-mbg' : ''; ?>">
                    <i class="fas fa-city me-2"></i> Supplier Vendor
                </a>
                <a href="index.php?page=stok_bahan" class="list-group-item list-group-item-action <?= $current_page === 'stok_bahan' ? 'active-mbg' : ''; ?>">
                    <i class="fas fa-boxes-stacked me-2"></i> Gudang Bahan
                </a>
                <a href="index.php?page=sekolah" class="list-group-item list-group-item-action <?= $current_page === 'sekolah' ? 'active-mbg' : ''; ?>">
                    <i class="fas fa-school me-2"></i> Data Sekolah
                </a>
                <a href="index.php?page=menu" class="list-group-item list-group-item-action <?= $current_page === 'menu' ? 'active-mbg' : ''; ?>">
                    <i class="fas fa-bowl-food me-2"></i> Menu Makanan
                </a>
                <a href="index.php?page=users" class="list-group-item list-group-item-action <?= $current_page === 'users' ? 'active-mbg' : ''; ?>">
                    <i class="fas fa-users-gear me-2"></i> Manajemen User
                </a>
            <?php endif; ?>

            <div class="nav-header text-uppercase">Logistik Dapur</div>
            <a href="index.php?page=detail_stok" class="list-group-item list-group-item-action <?= $current_page === 'detail_stok' ? 'active-mbg' : ''; ?>">
                <i class="fas fa-arrow-right-arrow-left me-2"></i> Transaksi Stok
            </a>
            <a href="index.php?page=distribusi" class="list-group-item list-group-item-action <?= $current_page === 'distribusi' ? 'active-mbg' : ''; ?>">
                <i class="fas fa-truck-fast me-2"></i> Distribusi Sekolah
            </a>
            
            <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');" class="list-group-item list-group-item-action btn-logout py-2">
                <i class="fas fa-power-off me-2"></i> Keluar Sistem
            </a>
        </div>
    </div>
</div>

<div class="col-md-9 ms-md-auto col-lg-10 px-md-4 py-4">