<?php
session_start();
require_once '../../config/database.php';

$response = ['status' => 'error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch();

    if ($user && $confirm_password === $user['password']) {
        // Hapus data
        $conn->prepare("DELETE FROM profil_kos WHERE id_user = ?")->execute([$id_user]);
        $conn->prepare("DELETE FROM users WHERE id_user = ?")->execute([$id_user]);

        session_destroy();
        $response['status'] = 'success';
    }
}

// Kirim hasil ke JavaScript
header('Content-Type: application/json');
echo json_encode($response);
exit;