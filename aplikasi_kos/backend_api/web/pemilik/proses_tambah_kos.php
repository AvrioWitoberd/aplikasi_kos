<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemilik = $_SESSION['user_id'];
    $nama_kos = $_POST['nama_kos'];
    $tipe_kos = $_POST['tipe_kos'];
    $harga = abs(intval($_POST['harga_per_bulan']));
    $alamat = $_POST['alamat_lengkap'];
    $kota = $_POST['kota'];
    $maps = $_POST['link_maps'];
    $fasilitas = $_POST['fasilitas_utama'];
    $deskripsi = $_POST['deskripsi'];
    $jumlah_kamar = abs(intval($_POST['jumlah_kamar']));
    $no_hp_kos = $_POST['no_hp_kos'];
    $peraturan_kos = $_POST['peraturan_kos'];
    $link_maps = $_POST['link_maps'];

        try {
                $conn->beginTransaction();

                $sql_kos = "INSERT INTO kos (id_pemilik, nama_kos, tipe_kos, alamat_lengkap, kota, harga_per_bulan, deskripsi, fasilitas_utama, jumlah_kamar, no_hp_kos, peraturan_kos, link_maps) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                // PERBAIKAN: Hapus satu tanda $
                $stmt = $conn->prepare($sql_kos); 
                
                $stmt->execute([
                    $id_pemilik, 
                    $nama_kos, 
                    $tipe_kos, 
                    $alamat,      // Gunakan $alamat (sesuai variabel di atas)
                    $kota, 
                    $harga, 
                    $deskripsi, 
                    $fasilitas, 
                    $jumlah_kamar, 
                    $no_hp_kos, 
                    $peraturan_kos, 
                    $link_maps
                ]);
                        
                $id_kos_baru = $conn->lastInsertId();

        // 2. Proses Upload Foto
        if (!empty($_FILES['foto_kos']['name'][0])) {
            $folder_upload = "../../uploads/kos/";
            $max_size = 2 * 1024 * 1024; // 2MB

            foreach ($_FILES['foto_kos']['tmp_name'] as $key => $tmp_name) {
                $file_size = $_FILES['foto_kos']['size'][$key];
                $file_name = $_FILES['foto_kos']['name'][$key];

                // Cek Ukuran
                if ($file_size > $max_size) {
                    // Jika ada satu saja yang lebih dari 2MB, lewati file ini
                    continue; 
                }

                $nama_file_baru = time() . "_" . $file_name;
                $target_path = $folder_upload . $nama_file_baru;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $sql_foto = "INSERT INTO foto_kos (id_kos, file_nama) VALUES (?, ?)";
                    $conn->prepare($sql_foto)->execute([$id_kos_baru, $nama_file_baru]);
                }
            }
        }

        $conn->commit();
        echo "sukses"; 
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Gagal menyimpan: " . $e->getMessage();
    }
}