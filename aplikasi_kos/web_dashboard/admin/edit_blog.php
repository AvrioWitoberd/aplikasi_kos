<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_blog = $_GET['id'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - MyKos Admin</title>
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
                <div class="d-flex align-items-start gap-3">
                    <a href="blog.php" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-top: 4px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Edit Berita</h2>
                        <p class="text-muted">Perbarui informasi berita yang sudah dipublikasikan.</p>
                    </div>
                </div>
            </div>

            <!-- Form Edit Blog -->
            <div id="editContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data berita...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const idBlog = "<?= $id_blog ?>";

// Load data blog dari API
async function loadBlog() {
    try {
        const response = await fetch(`../../backend_api/blog/get_blog_by_id.php?id=${idBlog}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            renderForm(result.data);
        } else {
            document.getElementById('editContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('editContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

// Render form edit
function renderForm(blog) {
    const html = `
        <form id="formEditBlog" enctype="multipart/form-data">
            <input type="hidden" name="id_blog" value="${blog.id_blog}">
            <input type="hidden" name="foto_lama" value="${blog.foto_thumbnail || ''}">
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-card-custom mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control py-2" value="${escapeHtml(blog.judul)}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Konten</label>
                            <textarea name="isi_konten" class="form-control" rows="12" required>${escapeHtml(blog.isi_konten)}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-card-custom mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select py-2">
                                <option value="All" ${blog.kategori === 'All' ? 'selected' : ''}>All</option>
                                <option value="News" ${blog.kategori === 'News' ? 'selected' : ''}>News</option>
                                <option value="Education" ${blog.kategori === 'Education' ? 'selected' : ''}>Education</option>
                                <option value="Sport" ${blog.kategori === 'Sport' ? 'selected' : ''}>Sport</option>
                                <option value="Info Kos" ${blog.kategori === 'Info Kos' ? 'selected' : ''}>Info Kos</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Thumbnail Berita</label>
                            <div class="border rounded-3 p-3 text-center bg-light">
                                ${blog.foto_thumbnail ? `<img id="preview" src="../../uploads/blog/${blog.foto_thumbnail}" class="preview-img mb-2">` : '<img id="preview" class="preview-img mb-2" style="display:none;">'}
                                <input type="file" name="foto_thumbnail" id="inputFoto" class="form-control" accept="image/*">
                                <small class="text-muted small">*Kosongkan jika tidak ingin mengubah foto</small>
                            </div>
                        </div>

                        <button type="submit" id="btnSubmit" class="btn-update w-100 py-3 fw-bold mt-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    `;
    
    document.getElementById('editContent').innerHTML = html;
    
    // Preview gambar
    const inputFoto = document.getElementById('inputFoto');
    const preview = document.getElementById('preview');
    if (inputFoto) {
        inputFoto.onchange = function() {
            const [file] = inputFoto.files;
            if (file) {
                if (preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            }
        };
    }
    
    // Submit form
    const form = document.getElementById('formEditBlog');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
            
            const formData = new FormData(form);
            
            try {
                const response = await fetch('../../backend_api/blog/update_blog.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    window.location.href = 'blog.php?status=updated';
                } else {
                    alert('Gagal: ' + result.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }
}

// Escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', loadBlog);
</script>

</body>
</html>