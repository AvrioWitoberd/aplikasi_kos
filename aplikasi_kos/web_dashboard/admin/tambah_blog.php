<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <!-- Header -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="blog.php" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Tambah Berita Baru</h2>
                        <p class="text-muted">Publikasikan berita terbaru untuk pengunjung MyKos.</p>
                    </div>
                </div>
            </div>

            <!-- Form Tambah Blog -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card-custom mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Berita</label>
                            <input type="text" name="judul" id="judul" class="form-control py-2" placeholder="Masukkan judul berita..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Konten</label>
                            <textarea name="isi_konten" id="isi_konten" class="form-control" rows="12" placeholder="Tulis konten berita di sini..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-card-custom mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select py-2" required>
                                <option value="All">All</option>
                                <option value="News">News</option>
                                <option value="Education">Education</option>
                                <option value="Sport">Sport</option>
                                <option value="Info Kos">Info Kos</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thumbnail</label>
                            <div class="border rounded-3 p-3 text-center bg-light">
                                <img id="preview" class="preview-img mb-2" style="display: none;">
                                <input type="file" name="foto_thumbnail" id="inputFoto" class="form-control" accept="image/*" required>
                                <small class="text-muted small">Format: JPG, PNG. Maks 2MB</small>
                            </div>
                        </div>

                        <button type="submit" id="btnSubmit" class="btn-update w-100 py-3 fw-bold mt-3 shadow-sm">
                            <i class="bi bi-send-fill me-2"></i>Terbitkan
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Preview gambar
const inputFoto = document.getElementById('inputFoto');
const preview = document.getElementById('preview');

inputFoto.onchange = function() {
    const [file] = inputFoto.files;
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
};

// Submit form via AJAX
const btnSubmit = document.getElementById('btnSubmit');
btnSubmit.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const judul = document.getElementById('judul').value;
    const isi_konten = document.getElementById('isi_konten').value;
    const kategori = document.getElementById('kategori').value;
    const foto = inputFoto.files[0];
    
    if (!judul || !isi_konten || !kategori || !foto) {
        alert('Semua field wajib diisi!');
        return;
    }
    
    const formData = new FormData();
    formData.append('judul', judul);
    formData.append('isi_konten', isi_konten);
    formData.append('kategori', kategori);
    formData.append('foto_thumbnail', foto);
    
    const originalText = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
    
    try {
        const response = await fetch('../../backend_api/blog/tambah_blog.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.href = 'blog.php?status=success';
        } else {
            alert('Gagal: ' + result.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;
    }
});
</script>

</body>
</html>