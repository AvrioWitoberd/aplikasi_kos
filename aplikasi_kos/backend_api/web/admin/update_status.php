<?php
require_once '../../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $status = $_POST['status'];

    $query = "UPDATE users SET status = :status WHERE id_user = :id";
    $stmt = $conn->prepare($query);
    if ($stmt->execute(['status' => $status, 'id' => $id_user])) {
        header("Location: dashboard.php?msg=success");
    } else {
        header("Location: dashboard.php?msg=error");
    }
}