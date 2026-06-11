<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

// =========================
// AMBIL SESSION LOGIN
// =========================

$id_user = $_SESSION['user_id'] ?? ($_POST['id_user'] ?? '');
$role = $_SESSION['role'] ?? ($_POST['role'] ?? '');

$id_kos = $_POST['id_kos'] ?? '';
$skor = $_POST['skor'] ?? 0;

// =========================
// VALIDASI LOGIN
// =========================

if (empty($id_user)) {

    echo json_encode([
        "status" => "login_required",
        "message" => "Harus login terlebih dahulu"
    ]);

    exit;
}

// =========================
// VALIDASI ROLE
// =========================

if ($role !== 'pencari') {

    echo json_encode([
        "status" => "forbidden",
        "message" => "Hanya pencari kos yang bisa memberikan rating"
    ]);

    exit;
}

// =========================
// VALIDASI INPUT
// =========================

if (empty($id_kos) || $skor < 1 || $skor > 5) {

    echo json_encode([
        "status" => "error",
        "message" => "Data rating tidak lengkap"
    ]);

    exit;
}

try {

    // =========================
    // CEK SUDAH RATING?
    // =========================

    $check = $conn->prepare("
        SELECT id_rating
        FROM rating
        WHERE id_user = ? AND id_kos = ?
    ");

    $check->execute([$id_user, $id_kos]);

    // =========================
    // UPDATE RATING
    // =========================

    if ($check->rowCount() > 0) {

        $update = $conn->prepare("
            UPDATE rating
            SET skor_rating = ?, created_at = NOW()
            WHERE id_user = ? AND id_kos = ?
        ");

        $update->execute([$skor, $id_user, $id_kos]);

        echo json_encode([
            "status" => "success",
            "action" => "updated",
            "message" => "Rating berhasil diperbarui"
        ]);

    } else {

        // =========================
        // INSERT RATING
        // =========================

        $insert = $conn->prepare("
            INSERT INTO rating
            (id_user, id_kos, skor_rating)
            VALUES (?, ?, ?)
        ");

        $insert->execute([$id_user, $id_kos, $skor]);

        echo json_encode([
            "status" => "success",
            "action" => "added",
            "message" => "Terima kasih telah memberikan rating!"
        ]);
    }

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>