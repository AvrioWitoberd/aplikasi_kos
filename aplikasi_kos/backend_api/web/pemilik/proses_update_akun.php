<?php
session_start(); // Memastikan session terbaca
require_once '../../config/database.php'; // Langsung ke database dulu

// Cek apakah session ada?
if (!isset($_SESSION['user_id'])) {
    die("Error: Session ID tidak ditemukan. Silakan login ulang.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user_id'];
    $new_email = trim($_POST['new_email']);
    $new_password = $_POST['new_password'];
    $old_password = $_POST['old_password'];

    try {
        // 1. Ambil data user asli dari DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch();

        if (!$user) {
            die("Error: User dengan ID $id_user tidak ada di database.");
        }

        // 2. Verifikasi Password Lama
        // Di database Abang (ID 9), passwordnya adalah 'admin123'
        if ($old_password === $user['password']) {
            
            if (!empty($new_password)) {
                // Update Email & Password
                $sql = "UPDATE users SET email = ?, password = ? WHERE id_user = ?";
                $stmtUpdate = $conn->prepare($sql);
                $stmtUpdate->execute([$new_email, $new_password, $id_user]);
            } else {
                // Update Email Saja
                $sql = "UPDATE users SET email = ? WHERE id_user = ?";
                $stmtUpdate = $conn->prepare($sql);
                $stmtUpdate->execute([$new_email, $id_user]);
            }

            // 3. Cek apakah benar-benar terupdate
            if ($stmtUpdate->rowCount() > 0) {
                header("Location: dashboard.php?msg=update_sukses");
            } else {
                // Ini terjadi jika data yang diinput SAMA dengan data lama
                header("Location: dashboard.php?msg=tidak_ada_perubahan");
            }
            exit;

        } else {
            // Jika ini muncul, berarti 'old_password' yang Abang ketik di modal salah
            header("Location: dashboard.php?msg=password_salah");
            exit;
        }

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}