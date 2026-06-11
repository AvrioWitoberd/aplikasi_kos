<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$tahun_ini = date('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

<main class="main-content">
    <!-- Header - SAMA PERSIS DENGAN DAFTAR KOS -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark mb-0">Kelola Blog</h2>
                <p class="text-muted">Visualisasi data postingan berita tahun <?= $tahun_ini ?>.</p>
            </div>
            <div class="text-end">
                <span class="text-muted small">Total Berita Terbit</span>
                <h4 class="fw-bold text-primary mb-0" id="totalBlog">0</h4>
            </div>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="mb-4">
        <a href="tambah_blog.php" class="btn btn-tambah-blog px-4 py-2 shadow-sm">
            <i class="bi bi-plus-circle-fill me-2"></i>Tambah Blog Baru
        </a>
    </div>

    <!-- Card Daftar Blog -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">Daftar Semua Berita</h5>
                <div class="d-flex gap-3">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-1" placeholder="Cari judul berita...">
                    </div>
                    <select id="filterKategori" class="form-select" style="width: 150px; border-radius: 8px;">
                        <option value="">All</option>
                        <option value="News">News</option>
                        <option value="Education">Education</option>
                        <option value="Sport">Sport</option>
                        <option value="Info Kos">Info Kos</option>
                    </select>
                </div>
            </div>

            <!-- Container Blog -->
            <div id="blogContainer">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data blog...</p>
                </div>
            </div>

            <!-- Footer Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Tampilkan</span>
                    <select id="limitEntries" class="form-select form-select-sm border-0 bg-light" style="width: auto; border-radius: 8px;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="all">Semua</option>
                    </select>
                    <span class="text-muted small ms-2">Data</span>
                </div>
                <div class="text-muted small">
                    Visible: <span id="countVisible" class="fw-bold text-primary">0</span> / <span id="totalVisible">0</span> Berita
                </div>
            </div>
        </div>
    </div>
</main>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Berita?</h5>
                <p class="text-muted small">Tindakan ini akan menghapus permanen berita <br><strong id="hapusJudul"></strong>.</p>
                <form id="formHapusBlog">
                    <input type="hidden" name="id_blog" id="hapusIdBlog">
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-light w-100 rounded-3 py-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger w-100 rounded-3 py-2">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Berhasil!</h5>
                <p class="text-muted small" id="successMessage">Data berhasil disimpan.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4 mt-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let allBlogData = [];
let modalHapus, modalSuccess;

// Fungsi format tanggal Indonesia
function tglIndo(tanggal) {
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const tgl = new Date(tanggal);
    return tgl.getDate() + ' ' + bulan[tgl.getMonth()] + ' ' + tgl.getFullYear();
}

// Load data blog dari API
async function loadBlog() {
    try {
        const response = await fetch('../../backend_api/blog/get_blog.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            allBlogData = result.data;
            document.getElementById('totalBlog').innerText = allBlogData.length;
            document.getElementById('totalVisible').innerText = allBlogData.length;
            renderBlog();
        } else {
            document.getElementById('blogContainer').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('blogContainer').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

// Render blog ke HTML
function renderBlog() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const kategori = document.getElementById('filterKategori').value;
    const limit = document.getElementById('limitEntries').value;
    const container = document.getElementById('blogContainer');
    
    let filteredData = allBlogData.filter(blog => {
        // Filter search
        const matchSearch = blog.judul.toLowerCase().includes(search);
        // Filter kategori
        const matchKategori = kategori === '' || blog.kategori === kategori;
        return matchSearch && matchKategori;
    });
    
    const totalFiltered = filteredData.length;
    document.getElementById('countVisible').innerText = totalFiltered;
    
    let max = (limit === 'all') ? filteredData.length : parseInt(limit);
    let displayedData = filteredData.slice(0, max);
    
    if (displayedData.length === 0) {
        container.innerHTML = '<div class="text-center py-5 text-muted">Belum ada berita yang dipublikasikan.</div>';
        return;
    }
    
    let html = '';
    displayedData.forEach(blog => {
        const foto = blog.foto_thumbnail ? `../../uploads/blog/${blog.foto_thumbnail}` : 'https://placehold.co/250x165?text=No+Image';
        const kategoriClass = blog.kategori === 'News' ? 'text-primary' : (blog.kategori === 'Tips & Trik' ? 'text-success' : 'text-warning');
        
        html += `
<div class="card blog-card border p-3 mb-4 blog-item">
    <div class="d-flex align-items-start">
        <a href="detail_blog.php?id=${blog.id_blog}" class="text-decoration-none">
            <img src="${foto}" class="blog-thumbnail me-4" alt="${escapeHtml(blog.judul)}" onerror="this.src='https://placehold.co/250x165?text=No+Image'">
        </a>
        <div class="flex-grow-1">
            <div class="mb-2">
                <span class="badge-kategori ${kategoriClass}">${escapeHtml(blog.kategori)}</span>
            </div>
            <a href="detail_blog.php?id=${blog.id_blog}" class="text-decoration-none">
                <h4 class="fw-semibold mb-2" style="font-weight: 600 !important; color: #1e293b;">${escapeHtml(blog.judul)}</h4>
            </a>
            <div class="text-muted small mb-3">
                <span><i class="bi bi-calendar3 me-2 text-primary"></i>${tglIndo(blog.tgl_dibuat)}</span>
                <span class="ms-3"><i class="bi bi-person me-2 text-primary"></i>Admin MyKos</span>
            </div>
            <p class="text-muted mb-0 blog-excerpt">${escapeHtml(blog.isi_konten.substring(0, 100))}${blog.isi_konten.length > 100 ? '...' : ''}</p>
        </div>
        <div class="d-flex flex-column gap-2 ms-3">
            <a href="edit_blog.php?id=${blog.id_blog}" class="btn-action-blog-vertikal shadow-sm" title="Edit">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <button type="button" class="btn-action-blog-vertikal btn-hapus-vertikal shadow-sm" onclick="openHapusModal(${blog.id_blog}, '${escapeHtml(blog.judul)}')" title="Hapus">
                <i class="bi bi-trash3-fill"></i> Hapus
            </button>
        </div>
    </div>
</div>
        `;
    });
    
    container.innerHTML = html;
}

// Fungsi hapus
let modalHapusInstance;

function openHapusModal(id, judul) {
    document.getElementById('hapusIdBlog').value = id;
    document.getElementById('hapusJudul').innerText = judul;
    modalHapusInstance.show();
}

// Fungsi show toast/success
function showSuccess(message) {
    document.getElementById('successMessage').innerText = message;
    modalSuccess.show();
}

// Filter & search
function updateFilter() {
    renderBlog();
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

// Event Listeners
document.getElementById('searchInput').addEventListener('keyup', updateFilter);
document.getElementById('limitEntries').addEventListener('change', updateFilter);
document.getElementById('filterKategori').addEventListener('change', updateFilter);

// Form hapus
const formHapusBlog = document.getElementById('formHapusBlog');
if (formHapusBlog) {
    formHapusBlog.addEventListener('submit', async (e) => {
        e.preventDefault();
        const idBlog = document.getElementById('hapusIdBlog').value;
        const formData = new FormData();
        formData.append('id_blog', idBlog);
        
        try {
            const response = await fetch('../../backend_api/blog/hapus_blog.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                modalHapusInstance.hide();
                showSuccess('Blog berhasil dihapus!');
                loadBlog(); // Reload data
            } else {
                alert('Gagal: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        }
    });
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    modalHapusInstance = new bootstrap.Modal(document.getElementById('modalHapus'));
    modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
    loadBlog();
    
    // Cek URL parameter untuk status sukses
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        showSuccess('Blog berhasil ditambahkan!');
        const cleanUrl = window.location.pathname;
        window.history.replaceState({}, '', cleanUrl);
    }
});
</script>

</body>
</html>