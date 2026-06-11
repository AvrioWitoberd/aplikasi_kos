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
    <title>Daftar Blog - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Daftar Blog</h2>
                        <p class="text-muted">Rekomendasi artikel dan berita terbaru untuk anda.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="search-wrapper" style="width: 280px;">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Cari judul berita...">
                        </div>
                        <select id="filterKategori" class="form-select filter-select" style="width: 150px;">
                            <option value="">All</option>
                            <option value="News">News</option>
                            <option value="Education">Education</option>
                            <option value="Sport">Sport</option>
                            <option value="Info Kos">Info Kos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid Blog - 3 kolom -->
            <div id="blogContainer" class="blog-grid">
                <div class="text-center p-5 w-100">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat artikel...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let allBlogData = [];

function formatDate(dateString) {
    const date = new Date(dateString);
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date.getDate() + ' ' + bulan[date.getMonth()] + ' ' + date.getFullYear();
}

async function loadBlog() {
    try {
        const response = await fetch('../../backend_api/blog/get_blog.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            allBlogData = result.data;
            renderBlog();
        } else {
            document.getElementById('blogContainer').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('blogContainer').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderBlog() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const kategori = document.getElementById('filterKategori').value;
    const container = document.getElementById('blogContainer');
    
    let filteredData = allBlogData.filter(blog => {
        const matchSearch = blog.judul.toLowerCase().includes(search);
        const matchKategori = kategori === '' || blog.kategori === kategori;
        return matchSearch && matchKategori;
    });
    
    if (filteredData.length === 0) {
        container.innerHTML = `
            <div class="text-center p-5 w-100">
                <i class="bi bi-journal-bookmark-fill fs-1 text-muted"></i>
                <p class="mt-2 text-muted">Belum ada artikel yang ditemukan.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    filteredData.forEach(blog => {
        const foto = blog.foto_thumbnail ? `../../uploads/blog/${blog.foto_thumbnail}` : 'https://placehold.co/400x250?text=No+Image';
        const badgeClass = `blog-badge-${blog.kategori.replace(/ /g, '')}`;
        
        html += `
            <div class="blog-card-vertikal">
                <img src="${foto}" class="blog-img-vertikal" onerror="this.src='https://placehold.co/400x250?text=No+Image'">
                <div class="p-3 d-flex flex-column flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <span class="blog-badge ${badgeClass}">${escapeHtml(blog.kategori)}</span>
                        <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i> ${formatDate(blog.tgl_dibuat)}</span>
                    </div>
                    <h5 class="blog-title-vertikal">${escapeHtml(blog.judul)}</h5>
                    <p class="blog-excerpt-vertikal">${escapeHtml(blog.isi_konten.substring(0, 100))}${blog.isi_konten.length > 100 ? '...' : ''}</p>
                    <div class="mt-auto pt-2">
                        <a href="detail_blog.php?id=${blog.id_blog}" class="btn-read-more d-inline-flex align-items-center">
                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
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

let debounceTimer;
function debounceFilter() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => renderBlog(), 300);
}
document.getElementById('searchInput').addEventListener('input', debounceFilter);
document.getElementById('filterKategori').addEventListener('change', renderBlog);

document.addEventListener('DOMContentLoaded', loadBlog);
</script>

</body>
</html>