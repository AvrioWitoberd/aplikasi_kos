<?php
require_once '../../config/database.php';
session_start();

// Matikan error agar tidak merusak output AJAX
error_reporting(0);
ini_set('display_errors', 0);

// Perbaikan Error Session: Cek semua kemungkinan kunci session
$id_pemilik = $_SESSION['id_user'] ?? $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

if (!$id_pemilik) {
    echo "error_session";
    exit;
}

// --- A. LOGIKA HAPUS FOTO LAMA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'hapus_foto_lama') {
    $id_foto = $_POST['id_foto'];
    $nama_file = $_POST['nama_file'];
    try {
        // Hapus hanya jika foto tersebut milik kos yang dimiliki user ini
        $stmt = $conn->prepare("DELETE f FROM foto_kos f JOIN kos k ON f.id_kos = k.id_kos WHERE f.id_foto = ? AND k.id_pemilik = ?");
        $stmt->execute([$id_foto, $id_pemilik]);
        
        if (file_exists("../../uploads/kos/" . $nama_file)) {
            unlink("../../uploads/kos/" . $nama_file);
        }
        echo "success_hapus";
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// --- B. LOGIKA UPDATE DATA & TAMBAH FOTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kos = $_POST['id_kos'];

    try {
        $conn->beginTransaction();

        // 1. Update Teks Utama
        $sql = "UPDATE kos SET 
                nama_kos = ?, jumlah_kamar = ?, tipe_kos = ?, kota = ?, 
                harga_per_bulan = ?, deskripsi = ?, alamat_lengkap = ?, 
                fasilitas_utama = ?, no_hp_kos = ?, link_maps = ?, 
                peraturan_kos = ?, area_sekitar_kos = ?
                WHERE id_kos = ? AND id_pemilik = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $_POST['nama_kos'], $_POST['jumlah_kamar'], $_POST['tipe_kos'], $_POST['kota'], 
            $_POST['harga_per_bulan'], $_POST['deskripsi'], $_POST['alamat_lengkap'], 
            $_POST['fasilitas_utama'], $_POST['no_hp_kos'], $_POST['link_maps'], 
            $_POST['peraturan_kos'], $_POST['area_sekitar_kos'],
            $id_kos, $id_pemilik
        ]);

        // 2. Tambah Foto Baru (Jika ada upload baru)
        if (isset($_FILES['foto_kos']) && !empty($_FILES['foto_kos']['name'][0])) {
            $upload_dir = "../../uploads/kos/";
            foreach ($_FILES['foto_kos']['name'] as $key => $name) {
                if ($_FILES['foto_kos']['error'][$key] == 0) {
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $new_name = "kos_" . time() . "_" . $key . "." . $ext;
                    if (move_uploaded_file($_FILES['foto_kos']['tmp_name'][$key], $upload_dir . $new_name)) {
                        
                        // PERBAIKAN: Gunakan 'file_nama' sesuai struktur DB Anda
                        $stmt_foto = $conn->prepare("INSERT INTO foto_kos (id_kos, file_nama) VALUES (?, ?)");
                        $stmt_foto->execute([$id_kos, $new_name]);
                    }
                }
            }
        }

        $conn->commit();
        echo "success"; // Ini akan memicu Modal Sukses muncul di JavaScript
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
    exit;
}