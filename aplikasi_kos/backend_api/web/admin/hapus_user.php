<?php
require_once '../../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    try {
        // Mulai transaksi (opsional, tapi bagus untuk keamanan data)
        $conn->beginTransaction();

        // 1. Hapus data kos milik user ini terlebih dahulu (karena ada relasi/FK)
        $queryKos = "DELETE FROM kos WHERE id_pemilik = :id";
        $stmtKos = $conn->prepare($queryKos);
        $stmtKos->execute(['id' => $id_user]);

        // 2. Hapus user
        $queryUser = "DELETE FROM users WHERE id_user = :id";
        $stmtUser = $conn->prepare($queryUser);
        $stmtUser->execute(['id' => $id_user]);

        $conn->commit();
        header("Location: dashboard.php?status=deleted");
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: dashboard.php?status=error");
    }
}