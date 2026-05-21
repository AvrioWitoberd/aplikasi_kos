<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Cek apakah email sudah terdaftar di database?
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        // --- KONDISI: USER BENER-BENER BARU (DAFTAR) ---
        $insert = $conn->prepare("INSERT INTO users (email, password, nama_lengkap, role, status) VALUES (?, ?, ?, 'pemilik', 'pending')");
        $insert->execute([$email, $password, 'Calon Pemilik']);
        
        $new_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $new_id;
        $_SESSION['role'] = 'pemilik';
        $_SESSION['nama'] = 'Calon Pemilik';

        header("Location: pemilik/isi_profil.php");
        exit;

    } else {
        // --- KONDISI: USER LAMA (LOGING ULANG) ---
        if ($password == $user['password']) {
            
            // Simpan data dasar ke session dulu agar bisa dicek profilnya
            $uid = $user['id_user'];

            // KHUSUS ADMIN: Langsung masuk tanpa cek profil/status pending
            if ($user['role'] == 'admin') {
                $_SESSION['user_id'] = $uid;
                $_SESSION['role'] = 'admin';
                $_SESSION['nama'] = $user['nama_lengkap'];
                header("Location: admin/dashboard.php");
                exit;
            }

            // --- LOGIKA BARU UNTUK PEMILIK ---
            
            // 1. Cek apakah sudah pernah isi profil?
            $stmtProfil = $conn->prepare("SELECT id_user FROM profil_kos WHERE id_user = ?");
            $stmtProfil->execute([$uid]);
            $profilExist = $stmtProfil->fetch();

            if (!$profilExist) {
                // JIKA BELUM ISI PROFIL: Langsung arahkan ke isi_profil (abaikan status pending)
                $_SESSION['user_id'] = $uid;
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama'] = $user['nama_lengkap'] ?? 'Calon Pemilik';
                header("Location: pemilik/isi_profil.php");
                exit;
            } else {
                // JIKA SUDAH ISI PROFIL: Baru kita cek status ACC dari Admin
                if ($user['status'] == 'pending') {
                    $error = "Akun anda dalam masa pengajuan, tunggu admin memvalidasi akun dalam waktu 24 jam.";
                } elseif ($user['status'] == 'nonaktif') {
                    $error = "Akun anda terdeteksi melakukan pelanggaran sehingga dinonaktifkan, silahkan ajukan banding dengan admin melalui ";
                    $link = '<a href="https://wa.me/6281332077170?text=Halo%20Admin%20MyKos,%20saya%20ingin%20melakukan%20banding%20atas%20akun%20saya%20yang%20dinonaktifkan" target="_blank" class="text-success fw-semibold">
                        <i class="bi bi-headset me-1"></i> Pusat Bantuan
                    </a>';
                    $error .= $link . '.';
                    } else {
                    // STATUS AKTIF: Boleh masuk Dashboard
                    $_SESSION['user_id'] = $uid;
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['nama'] = $user['nama_lengkap'];
                    header("Location: pemilik/dashboard.php");
                    exit;
                }
            }
        } else {
            $error = "Email sudah terdaftar, tapi password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .form-control::placeholder {
            color: #adb5bd !important;
            opacity: 1;
        }
        .input-group {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            transition: 0.3s;
        }
        .input-group:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }
        .form-control {
            border: none !important;
            padding: 0.8rem 1rem 0.8rem 0.5rem;
            box-shadow: none !important;
        }
        .input-group-text {
            background-color: transparent;
            border: none;
            padding-left: 1rem;
            color: #6c757d;
        }
        .btn-login {
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 500;
            background-color: #0d6efd;
            border: none;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <i class="bi bi-house-door-fill text-primary" style="font-size: 3rem;"></i>
        <h2 class="fw-bold text-primary mb-0">MyKos</h2>
        <p class="text-muted small">Panel Admin & Pemilik Kos</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-warning small py-2 mb-3" role="alert" style="line-height: 1.4;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" id="loginForm">
        <div class="mb-3">
            <label for="email" class="form-label small fw-medium">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="nama@email.com" autocomplete="username" required>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label small fw-medium">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="••••••••" autocomplete="current-password" required>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-login shadow-sm">
                Masuk Sekarang <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
        </div>
    </form>
</div>

</body>
</html>