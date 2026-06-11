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
    <title>Detail Berita - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div id="detailContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat detail berita...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const idBlog = "<?= $id_blog ?>";

// Fungsi format tanggal Indonesia
function tglIndo(tanggal) {
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const tgl = new Date(tanggal);
    return tgl.getDate() + ' ' + bulan[tgl.getMonth()] + ' ' + tgl.getFullYear();
}

async function loadDetail() {
    try {
        const response = await fetch(`../../backend_api/blog/get_detail_blog.php?id=${idBlog}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            renderDetail(result.data);
        } else {
            document.getElementById('detailContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('detailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderDetail(blog) {
    const tglFormatted = tglIndo(blog.tgl_dibuat);
    const foto = blog.foto_thumbnail ? `../../uploads/blog/${blog.foto_thumbnail}` : 'https://placehold.co/1200x600?text=No+Image';
    
    // Warna badge kategori
    let kategoriClass = 'bg-primary';
    if (blog.kategori === 'News') kategoriClass = 'bg-primary';
    else if (blog.kategori === 'Education') kategoriClass = 'bg-success';
    else if (blog.kategori === 'Sport') kategoriClass = 'bg-danger';
    else if (blog.kategori === 'Info Kos') kategoriClass = 'bg-info';
    else kategoriClass = 'bg-secondary';
    
    const html = `
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-dark mb-0">Detail Berita</h2>
                    <p class="text-muted">Informasi lengkap berita yang dipublikasikan.</p>
                </div>
            </div>
        </div>

        <div class="card detail-blog-card">
            <div class="detail-blog-image">
                <img src="${foto}" alt="${escapeHtml(blog.judul)}" onerror="this.src='https://placehold.co/1200x600?text=No+Image'">
            </div>
            
            <div class="card-body p-4 p-lg-5">
                <!-- Judul -->
                <h1 class="detail-blog-title mb-4">${escapeHtml(blog.judul)}</h1>
                
                <!-- Baris Kategori, Tanggal, dan Share -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div class="d-flex gap-3 align-items-center">
                        <span class="badge ${kategoriClass} detail-blog-badge">${escapeHtml(blog.kategori)}</span>
                        <span class="text-muted small"><i class="bi bi-calendar3 me-2"></i>${tglFormatted}</span>
                    </div>
                    <button class="btn btn-outline-secondary btn-share" onclick="bagikanBerita()">
                        <i class="bi bi-share me-1"></i> Bagikan
                    </button>
                </div>
                
                <!-- Konten -->
                <div class="detail-blog-content">
                    ${nl2br(escapeHtml(blog.isi_konten))}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = html;
}

// Fungsi Bagikan
function bagikanBerita() {
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: url
        }).catch(err => console.log('Share cancelled:', err));
    } else {
        navigator.clipboard.writeText(url);
        alert('Link berhasil disalin!');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function nl2br(str) {
    if (!str) return '';
    return str.replace(/\n/g, '<br>');
}

document.addEventListener('DOMContentLoaded', loadDetail);
</script>

</body>
</html>