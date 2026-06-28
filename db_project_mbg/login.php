<?php
require_once 'controllers/AuthController.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Database::sanitize($_POST['username']);
    $password = $_POST['password'];

    $auth = new AuthController();
    if ($auth->login($username, $password)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MBG System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            /* Latar belakang bertema persiapan makanan sehat / dapur profesional hg hq dengan overlay emerald gelap */
            background: 
                linear-gradient(135deg, rgba(6, 28, 29, 0.92) 0%, rgba(3, 15, 16, 0.95) 100%),
                url('https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=1200&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }
        
        /* Card Utama dengan efek Glassmorphism Premium */
        .login-card {
            border-radius: 24px !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4) !important;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.98);
        }

        /* Bagian Header Card berlatar belakang Deep Emerald Night */
        .login-header {
            background: linear-gradient(180deg, #082728 0%, #061c1d 100%);
            padding: 40px 20px 35px 20px;
            color: #ffffff;
            border-bottom: 2px solid rgba(16, 185, 129, 0.2);
        }

        /* Form Input styling */
        .form-control {
            border-radius: 12px !important;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px !important;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        /* Tombol Utama dengan Gradasi Hijau Segar aktif */
        .btn-mbg-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 600;
            border-radius: 12px !important;
            padding: 12px 20px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            transition: all 0.25s ease;
            letter-spacing: 0.5px;
        }
        .btn-mbg-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #ffffff !important;
        }
        .btn-mbg-primary:active {
            transform: translateY(0);
        }

        /* Notifikasi error kustom */
        .alert-mbg-danger {
            background: rgba(239, 68, 68, 0.08) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.15) !important;
            border-radius: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body class="d-flex align-items-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <!-- Header Branding Sistem -->
                <div class="login-header text-center">
                    <h3 class="fw-bold mb-1 tracking-wide">
                        <i class="fas fa-utensils me-2 text-success"></i> MBG LOGIN SYSTEM
                    </h3>
                    <p class="small mb-0" style="color: #8da1a2 !important;">Makanan Bergizi Gratis Portal Logistik</p>
                </div>
                
                <!-- Konten Form Login -->
                <div class="card-body p-4 pt-4">
                    <div class="text-center mb-4">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider" style="font-size: 0.75rem; color: #061c1d !important;">
                            <i class="fas fa-kitchen-set me-1 text-success"></i> Management Portal
                        </span>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-mbg-danger py-2.5 small d-flex align-items-center mb-4">
                            <i class="fas fa-triangle-exclamation me-2 fs-6"></i> <?= $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="fas fa-user text-muted small"></i></span>
                                <input type="text" name="username" class="form-control border-start-0" style="border-radius: 0 12px 12px 0 !important;" placeholder="Masukkan username" required autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0"><i class="fas fa-lock text-muted small"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" style="border-radius: 0 12px 12px 0 !important;" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-mbg-primary w-100 py-2.5 shadow-sm">
                            Masuk Ke Sistem <i class="fas fa-arrow-right ms-1 small"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- Footer Penanda Hak Cipta -->
            <p class="text-center small mt-4" style="color: #a0b6b7 !important; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">&copy; <?= date('Y'); ?> MBG Sistem. All Rights Reserved.</p>
        </div>
    </div>
</div>

</body>
</html>