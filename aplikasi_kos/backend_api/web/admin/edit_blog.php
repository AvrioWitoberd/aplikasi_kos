<?php
require_once '../../config/database.php';
session_start();

// 1. Ambil ID blog yang mau diedit
if (!isset($_GET['id'])) {
    header("Location: blog.php");
    exit();
}

$id_blog = $_GET['id'];
$id_admin_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 2; // Fallback ke ID admin abang

// 2. Tarik data lama dari database
try {
    $query = "SELECT * FROM blog WHERE id_blog = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_blog]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        die("Data tidak ditemukan!");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. Proses Update Data
if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $isi_konten = $_POST['isi_konten'];
    $foto_lama = $_POST['foto_lama'];

    // Cek apakah ada upload foto baru
    if ($_FILES['foto_thumbnail']['name'] != "") {
        $foto_name = $_FILES['foto_thumbnail']['name'];
        $tmp_name = $_FILES['foto_thumbnail']['tmp_name'];
        $unique_name = time() . "_" . $foto_name;
        $target_dir = "../../uploads/blog/";

        move_uploaded_file($tmp_name, $target_dir . $unique_name);
        
        // Hapus foto lama agar tidak nyampah di server
        if (file_exists($target_dir . $foto_lama)) {
            unlink($target_dir . $foto_lama);
        }
        $foto_final = $unique_name;
    } else {
        $foto_final = $foto_lama; // Tetap pakai foto lama
    }

    // Bagian paling bawah proses UPDATE di edit_blog.php
    try {
        $sql = "UPDATE blog SET judul = ?, kategori = ?, isi_konten = ?, foto_thumbnail = ? WHERE id_blog = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$judul, $kategori, $isi_konten, $foto_final, $id_blog]);

        // Redireksi dengan membawa parameter status=updated
        header("Location: blog.php?status=updated");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal update: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .form-card { border: none; border-radius: 20px; }
        .preview-img { width: 100%; max-height: 250px; object-fit: cover; border-radius: 15px; margin-bottom: 10px; }
        .btn-update { background-color: #0d6efd; color: #fff; border-radius: 12px; font-weight: 600; transition: all 0.3s; }
        .btn-update:hover { background-color: #0b5ed7; transform: translateY(-2px); }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-4 py-3">
        <div class="d-flex align-items-center mb-4">
            <a href="blog.php" class="btn btn-light rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Edit Berita</h3>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="foto_lama" value="<?= $blog['foto_thumbnail'] ?>">
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card form-card shadow-sm p-4 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control py-2" value="<?= htmlspecialchars($blog['judul']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Konten</label>
                            <textarea name="isi_konten" class="form-control" rows="12" required><?= htmlspecialchars($blog['isi_konten']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card form-card shadow-sm p-4 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select py-2">
                                <option value="News" <?= $blog['kategori'] == 'News' ? 'selected' : '' ?>>News</option>
                                <option value="Tips & Trik" <?= $blog['kategori'] == 'Tips & Trik' ? 'selected' : '' ?>>Tips & Trik</option>
                                <option value="Promo" <?= $blog['kategori'] == 'Promo' ? 'selected' : '' ?>>Promo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thumbnail Berita</label>
                            <div class="border rounded-3 p-2 text-center">
                                <img id="preview" src="../../uploads/blog/<?= $blog['foto_thumbnail'] ?>" class="preview-img">
                                <input type="file" name="foto_thumbnail" id="inputFoto" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted small">*Kosongkan jika tidak ingin ganti foto</small>
                        </div>

                        <button type="submit" name="update" class="btn btn-update w-100 py-3 mt-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview gambar saat ganti file
    inputFoto.onchange = evt => {
        const [file] = inputFoto.files
        if (file) {
            preview.src = URL.createObjectURL(file)
        }
    }
</script>

</body>
</html>