<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once '../config/database.php';

// Ambil input POST (sesuai dengan auth_service.dart)
$nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : "";
$no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : "";
$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$password = isset($_POST['password']) ? trim($_POST['password']) : "";

// Validasi input
if (empty($nama_lengkap) || empty($no_hp) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Harap lengkapi semua kolom!"]);
    exit;
}

try {
    // Cek apakah email sudah terdaftar
    $query_cek = "SELECT id_user FROM users WHERE email = :email";
    $stmt_cek = $conn->prepare($query_cek);
    $stmt_cek->execute(['email' => $email]);
    
    if ($stmt_cek->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar. Silakan gunakan email lain atau login."]);
        exit;
    }

    // Insert user baru 
    // Catatan: password disimpan dalam plain-text agar konsisten dengan login_pencari.php
    $query = "INSERT INTO users (nama_lengkap, no_hp, email, password, role, status) 
              VALUES (:nama_lengkap, :no_hp, :email, :password, 'pencari', 'aktif')";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'nama_lengkap' => $nama_lengkap,
        'no_hp' => $no_hp,
        'email' => $email,
        'password' => $password
    ]);

    // Ambil ID user yang baru ditambahkan
    $new_id = $conn->lastInsertId();

    // Set sesi
    $_SESSION['user_id'] = $new_id;
    $_SESSION['role'] = 'pencari';

    // Response sukses (harus ada "data" object karena AuthService Dart memanggil data['data'])
    echo json_encode([
        "status" => "success",
        "data" => [
            "id_user" => $new_id,
            "nama_lengkap" => $nama_lengkap,
            "no_hp" => $no_hp,
            "email" => $email,
            "role" => "pencari"
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error", 
        "message" => "Database Error: " . $e->getMessage()
    ]);
}
?>
