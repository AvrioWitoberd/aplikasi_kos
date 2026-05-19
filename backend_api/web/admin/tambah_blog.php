<?php
require_once '../../config/database.php';
session_start();

// Perbaikan: Ambil ID dari session login, jika belum ada kita pakai ID 2 (ID admin abang)
$id_admin_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 2;

if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $isi_konten = $_POST['isi_konten'];
    $tgl_dibuat = date('Y-m-d H:i:s');

    // Proses Upload Foto
    $foto_name = $_FILES['foto_thumbnail']['name'];
    $tmp_name = $_FILES['foto_thumbnail']['tmp_name'];
    $unique_name = time() . "_" . $foto_name;
    $target_dir = "../../uploads/blog/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_name, $target_dir . $unique_name)) {
        try {
            // Gunakan ID yang valid agar tidak error Foreign Key lagi
            $query = "INSERT INTO blog (id_admin, judul, foto_thumbnail, kategori, isi_konten, tgl_dibuat) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id_admin_login, $judul, $unique_name, $kategori, $isi_konten, $tgl_dibuat]);
            
            header("Location: blog.php?status=success");
            exit();
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Gagal mengunggah foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulis Berita - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .form-card { border: none; border-radius: 20px; }
        .preview-img { width: 100%; max-height: 250px; object-fit: cover; border-radius: 15px; display: none; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-4 py-3">
        <div class="d-flex align-items-center mb-4">
            <a href="blog.php" class="btn btn-light rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Tulis Berita Baru</h3>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger shadow-sm"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card form-card shadow-sm p-4 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control py-2" placeholder="Masukkan judul berita..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Konten</label>
                            <textarea name="isi_konten" class="form-control" rows="10" placeholder="Tulis konten di sini..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card form-card shadow-sm p-4 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select py-2" required>
                                <option value="News">News</option>
                                <option value="Tips & Trik">Tips & Trik</option>
                                <option value="Promo">Promo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thumbnail</label>
                            <div class="border rounded-3 p-2 text-center">
                                <img id="preview" class="preview-img mb-2">
                                <input type="file" name="foto_thumbnail" id="inputFoto" class="form-control" accept="image/*" required>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary w-100 py-3 fw-bold mt-3" style="border-radius: 12px;">
                            <i class="bi bi-send-fill me-2"></i>Terbitkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    inputFoto.onchange = evt => {
        const [file] = inputFoto.files
        if (file) {
            preview.src = URL.createObjectURL(file)
            preview.style.display = 'block'
        }
    }
</script>

</body>
</html>