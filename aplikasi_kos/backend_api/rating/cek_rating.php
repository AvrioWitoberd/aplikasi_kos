<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

$id_kos = $_GET['id'] ?? '';
$id_user = $_GET['id_user'] ?? '';

if (empty($id_kos)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID Kos tidak ditemukan"
    ]);
    exit;
}

$response = [
    "status" => "success",
    "user_rating" => null,
    "avg_rating" => 0,
    "total_ulasan" => 0
];

try {

    // rata-rata rating
    $query = $conn->prepare("
        SELECT 
            AVG(skor_rating) as rata_rating,
            COUNT(*) as total_ulasan
        FROM rating
        WHERE id_kos = ?
    ");

    $query->execute([$id_kos]);

    $rating_data = $query->fetch(PDO::FETCH_ASSOC);

    $response['avg_rating'] = number_format($rating_data['rata_rating'] ?? 0, 1);
    $response['total_ulasan'] = $rating_data['total_ulasan'] ?? 0;

    // cek rating user
    if (!empty($id_user)) {

        $user_rating = $conn->prepare("
            SELECT skor_rating
            FROM rating
            WHERE id_user = ? AND id_kos = ?
        ");

        $user_rating->execute([$id_user, $id_kos]);

        $user_rating_data = $user_rating->fetch(PDO::FETCH_ASSOC);

        if ($user_rating_data) {
            $response['user_rating'] = (int)$user_rating_data['skor_rating'];
        }
    }

    echo json_encode($response);

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>