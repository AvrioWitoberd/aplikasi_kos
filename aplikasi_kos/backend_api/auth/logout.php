<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

session_start();

// Hapus semua session
$_SESSION = [];

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

echo json_encode([
    "status" => "success",
    "message" => "Logout berhasil"
]);
?>