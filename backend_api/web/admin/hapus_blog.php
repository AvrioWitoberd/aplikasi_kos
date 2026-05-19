<?php
require_once '../../config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id_blog = $_GET['id'];

    try {
        // 1. Ambil nama file foto dulu buat dihapus dari folder uploads
        $query_foto = "SELECT foto_thumbnail FROM blog WHERE id_blog = ?";
        $stmt_foto = $conn->prepare($query_foto);
        $stmt_foto->execute([$id_blog]);
        $data = $stmt_foto->fetch();

        if ($data) {
            $path = "../../uploads/blog/" . $data['foto_thumbnail'];
            if (file_exists($path)) {
                unlink($path); // Hapus file foto dari server
            }

            // 2. Hapus data dari database
            $query_hapus = "DELETE FROM blog WHERE id_blog = ?";
            $stmt_hapus = $conn->prepare($query_hapus);
            $stmt_hapus->execute([$id_blog]);
        }

        header("Location: blog.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        die("Gagal menghapus: " . $e->getMessage());
    }
} else {
    header("Location: blog.php");
    exit();
}