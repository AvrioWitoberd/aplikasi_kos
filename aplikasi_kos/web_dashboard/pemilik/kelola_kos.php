<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kos - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="stylesheet" href="../assets/css/style_mobile.css?v=9">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- MOBILE TOPBAR -->
<div class="mobile-topbar d-lg-none">
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <h5 class="mb-0 fw-bold text-primary">MyKos</h5>
</div>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div id="kelolaContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data kos...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 24px;">
            <div class="modal-header border-0 pt-4 pb-0 position-relative">
                <h5 class="modal-title fw-bold text-danger w-100 text-center">Hapus Kos</h5>
                <button type="button" class="btn-close position-absolute end-0 top-0 me-3 mt-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 65px; height: 65px;">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Apakah Anda yakin?</h5>
                <p class="mb-0">Ingin menghapus kos <strong id="namaKosHapus" class="text-danger"></strong>?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnKonfirmasiHapus" class="btn btn-danger px-4 py-2 rounded-pill">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let kosData = [];
let hapusId = null;
let modalHapusInstance;

async function loadKelolaKos() {
    try {
        const response = await fetch('../../backend_api/pemilik/kelola_kos.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            kosData = result.data;
            renderKelolaKos();
        } else {
            document.getElementById('kelolaContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('kelolaContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderKelolaKos() {
    if (kosData.length === 0) {
        document.getElementById('kelolaContent').innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Daftar Properti Kos</h3>
                    <p class="text-muted small">Kelola ketersediaan unit kos Anda.</p>
                </div>
                <a href="tambah_kos.php" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Unit
                </a>
            </div>
            <div class="kelola-card">
                <div class="text-center py-5">
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <p class="mt-2 text-muted">Belum ada unit kos yang ditambahkan.</p>
                </div>
            </div>
        `;
        return;
    }
    
    let tableRows = '';
    kosData.forEach((kos, index) => {
        const fotoPath = kos.foto_utama ? `../../uploads/foto_kos/${kos.foto_utama}` : 'https://placehold.co/70x70?text=No+Image';
        const stokStatus = kos.jumlah_kamar > 0 
            ? `<span class="text-success fw-bold">${kos.jumlah_kamar} Kamar</span>`
            : `<span class="text-danger fw-bold">Penuh</span>`;
        
        // Potong alamat jika terlalu panjang
        let alamat = kos.alamat_lengkap || '-';
        if (alamat.length > 80) {
            alamat = alamat.substring(0, 80) + '...';
        }
        
        tableRows += `
            <tr>
                <td class="ps-4">${index + 1}.</td>
                <td>
                    <div class="kelola-unit-container">
                        <img src="${fotoPath}" class="kelola-foto" alt="Foto" onerror="this.src='https://placehold.co/70x70?text=No+Image'">
                        <div class="kelola-unit-info">
                            <div class="nama-kos">${escapeHtml(kos.nama_kos)}</div>
                            <div class="alamat-kos" title="${escapeHtml(kos.alamat_lengkap || '')}">${escapeHtml(alamat)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div><span class="badge bg-light text-primary border border-primary px-3 py-1">${escapeHtml(kos.tipe_kos).toUpperCase()}</span></div>
                    <div class="small text-muted mt-1">${escapeHtml(kos.kota || '-')}</div>
                </td>
                <td class="fw-semibold text-dark">Rp ${new Intl.NumberFormat('id-ID').format(kos.harga_per_bulan)}</td>
                <td>${stokStatus}</td>
                <td class="text-center">
                    <div class="kelola-actions">
                        <a href="detail_kos.php?id=${kos.id_kos}" class="btn btn-outline-primary btn-action-kelola" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="edit_kos.php?id=${kos.id_kos}" class="btn btn-outline-warning btn-action-kelola" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button onclick="openHapusModal(${kos.id_kos}, '${escapeHtml(kos.nama_kos)}')" class="btn btn-outline-danger btn-action-kelola" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    const html = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Daftar Properti Kos</h3>
                <p class="text-muted small">Kelola ketersediaan unit kos Anda.</p>
            </div>
            <a href="tambah_kos.php" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-plus-lg me-2"></i>Tambah Unit
            </a>
        </div>

        <div class="kelola-card">
            <div class="table-responsive">
                <table class="kelola-table table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>Unit Kos</th>
                            <th>Tipe & Kota</th>
                            <th>Harga /Bulan</th>
                            <th>Sisa Kamar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('kelolaContent').innerHTML = html;
}

function openHapusModal(id, nama) {
    hapusId = id;
    document.getElementById('namaKosHapus').innerText = nama;
    modalHapusInstance.show();
}

async function hapusKos() {
    if (!hapusId) return;
    
    const formData = new FormData();
    formData.append('id_kos', hapusId);
    
    try {
        const response = await fetch('../../backend_api/pemilik/hapus_kos.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            modalHapusInstance.hide();
            showToast('Kos berhasil dihapus!', 'success');
            loadKelolaKos();
        } else {
            showToast('Gagal: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `position-fixed bottom-0 end-0 p-3 m-3 rounded-3 shadow text-white ${type === 'success' ? 'bg-success' : 'bg-danger'}`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
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

document.addEventListener('DOMContentLoaded', function() {
    modalHapusInstance = new bootstrap.Modal(document.getElementById('modalHapus'));
    document.getElementById('btnKonfirmasiHapus').addEventListener('click', hapusKos);
    loadKelolaKos();
});
</script>

<script>
const menuBtn = document.getElementById('mobileMenuBtn');
const sidebar = document.querySelector('.sidebar');
const overlay = document.getElementById('sidebarOverlay');

if(menuBtn){
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show-sidebar');
        overlay.classList.toggle('show-overlay');
    });
}

if(overlay){
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show-sidebar');
        overlay.classList.remove('show-overlay');
    });
}
</script>

<script>
const btnLogoutMobile = document.getElementById('btnLogoutMobile');

if(btnLogoutMobile){

    btnLogoutMobile.addEventListener('click', () => {

        // khusus mobile
        if(window.innerWidth <= 991.98){

            mobileSidebar.classList.remove('show-sidebar');

            sidebarOverlay.classList.remove('show-overlay');

        }

    });

}
</script>

</body>
</html>