<?php
header("Content-Type: application/json");
require_once '../config/database.php';

$id_target = $_GET['id'] ?? '';

if (empty($id_target)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}

try {
    // Ambil Data Profil & User
    $query = $conn->prepare("SELECT u.id_user, u.email, u.status, u.role, u.created_at,
                                    p.id_profil, p.nama_pemilik, p.nama_kos, p.deskripsi, 
                                    p.alamat_lengkap, p.kota, p.kontak, p.foto_kos, p.foto_ktp, p.bukti_bayar
                             FROM users u
                             LEFT JOIN profil_kos p ON u.id_user = p.id_user 
                             WHERE u.id_user = ?");
    $query->execute([$id_target]);
    $user_data = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
        exit;
    }

    // Ambil Daftar Kos
    $query_kos = $conn->prepare("SELECT * FROM kos WHERE id_pemilik = ?");
    $query_kos->execute([$id_target]);
    $daftar_kos = $query_kos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $user_data,
        "kos" => $daftar_kos
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}