<?php
header("Content-Type: application/json");
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';

    if (empty($id_user)) {
        echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
        exit;
    }

    try {
        // Ambil dulu nama-nama file yang akan dihapus
        $stmt = $conn->prepare("SELECT foto_ktp, foto_kos, bukti_bayar FROM profil_kos WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $files = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $conn->beginTransaction();
        
        // Hapus data di profil_kos
        $delProfil = $conn->prepare("DELETE FROM profil_kos WHERE id_user = ?");
        $delProfil->execute([$id_user]);
        
        // Hapus data di users
        $delUser = $conn->prepare("DELETE FROM users WHERE id_user = ?");
        $delUser->execute([$id_user]);
        
        $conn->commit();
        
        // Hapus file fisik setelah transaksi berhasil
        if ($files) {
            // Tentukan path folder uploads (sesuaikan dengan struktur Anda)
            $basePath = __DIR__ . '/../uploads/';
            
            // Hapus foto KTP
            if (!empty($files['foto_ktp'])) {
                $filePath = $basePath . 'ktp/' . $files['foto_ktp'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Hapus foto Kos (bisa multiple, dipisah koma)
            if (!empty($files['foto_kos'])) {
                $fotoList = explode(',', $files['foto_kos']);
                foreach ($fotoList as $foto) {
                    $foto = trim($foto);
                    if (!empty($foto)) {
                        $filePath = $basePath . 'kos/' . $foto;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }
            
            // Hapus bukti bayar
            if (!empty($files['bukti_bayar'])) {
                $filePath = $basePath . 'bukti_bayar/' . $files['bukti_bayar'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        
        echo json_encode(["status" => "success", "message" => "Akun dan semua file terkait berhasil dihapus"]);
        
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
}
?>