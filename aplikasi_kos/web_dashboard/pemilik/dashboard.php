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
    <title>Dashboard Pemilik - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="stylesheet" href="../assets/css/style_mobile.css?v=1.1">
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

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div id="dashboardContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data dashboard...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Reset Akun -->
<div class="modal fade" id="modalResetAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Reset Email & Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResetAkun">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Baru</label>
                        <input type="email" name="new_email" class="form-control" placeholder="Masukkan email baru" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti password">
                    </div>
                    <div class="bg-light p-3 rounded-3">
                        <label class="form-label small fw-bold text-danger">Konfirmasi Password Lama</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Wajib isi password saat ini" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Akun -->
<div class="modal fade" id="modalHapusAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formHapusAkun">
                <div class="modal-body py-3">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-octagon text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center fw-bold mb-1">Tindakan ini tidak dapat dibatalkan!</p>
                    <p class="text-muted small text-center mb-4">Seluruh data kos dan profil Anda akan dihapus permanen.</p>
                    <div class="bg-light p-3 rounded-3">
                        <label class="form-label small fw-bold">Masukkan Password Anda</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Ketik password untuk hapus" required>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold">Ya, Hapus Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
async function loadDashboard() {
    try {
        const response = await fetch('../../backend_api/pemilik/dashboard.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            renderDashboard(result.data);
        } else {
            document.getElementById('dashboardContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('dashboardContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderDashboard(data) {
    const html = `
        <div class="mb-4">
            <h3 class="fw-bold">Selamat Datang! 👋</h3>
            <p class="text-muted">Berikut adalah ringkasan properti kos Anda hari ini.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card-pemilik bg-primary text-white p-3">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h6 class="mb-1 opacity-75">Total Unit Kos</h6>
                            <h2 class="stat-number-pemilik mb-0">${data.total_kos}</h2>
                        </div>
                        <i class="bi bi-building stat-icon-pemilik"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card-pemilik bg-success text-white p-3">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h6 class="mb-1 opacity-75">Total Stok Kamar</h6>
                            <h2 class="stat-number-pemilik mb-0">${data.total_stok}</h2>
                        </div>
                        <i class="bi bi-door-open stat-icon-pemilik"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card-pemilik bg-warning text-dark p-3">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h6 class="mb-1 opacity-75">Status Akun</h6>
                            <h4 class="fw-semibold mb-0">Pemilik Aktif</h4>
                        </div>
                        <i class="bi bi-patch-check-fill stat-icon-pemilik"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
<div class="col-lg-8">
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Unit Kos Terbaru</h5>
                <a href="kelola_kos.php" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua Kos Saya</a>
            </div>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 unit-kos-table" style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; color: #475569; border-bottom: 2px solid #e2e8f0;">Nama Kos</th>
                            <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; color: #475569; border-bottom: 2px solid #e2e8f0;">Kota</th>
                            <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; color: #475569; border-bottom: 2px solid #e2e8f0;">Tipe</th>
                            <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; color: #475569; border-bottom: 2px solid #e2e8f0;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.kos_terbaru.length > 0 ? data.kos_terbaru.map(k => `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px;">
                                    <strong style="font-size: 15px; color: #1e293b;">${escapeHtml(k.nama_kos)}</strong>
                                </td>
                                <td style="padding: 14px 16px; color: #475569;">
                                    ${escapeHtml(k.kota || '-')}
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="display: inline-block; padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 30px; font-size: 12px; font-weight: 500;">
                                        ${escapeHtml(k.tipe_kos)}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: #0d6efd; white-space: nowrap;">
                                    Rp ${new Intl.NumberFormat('id-ID').format(k.harga_per_bulan)}
                                </td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="4" style="padding: 40px; text-align: center; color: #94a3b8;">
                                    Belum ada data kos yang ditambahkan.
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

            <div class="col-lg-4">
                <div class="card menu-card-pemilik">
                    <h5 class="fw-bold mb-3">Menu Cepat</h5>
                    <div class="d-flex flex-column">
                        <a href="tambah_kos.php" class="help-link-pemilik text-dark">
                            <i class="bi bi-plus-circle text-primary fs-5" style="width: 28px;"></i> Tambah Unit Baru
                        </a>
                        <a href="#" class="help-link-pemilik text-dark" data-bs-toggle="modal" data-bs-target="#modalResetAkun">
                            <i class="bi bi-person-gear text-primary fs-5" style="width: 28px;"></i> Reset Email & Password
                        </a>
                        <a href="https://wa.me/6281332077170?text=Halo%20Admin%20MyKos,%20saya%20ingin%20bertanya..." target="_blank" class="help-link-pemilik text-dark">
                            <i class="bi bi-headset text-success fs-5" style="width: 28px;"></i> Pusat Bantuan
                        </a>
                        <a href="#" class="help-link-pemilik text-danger" data-bs-toggle="modal" data-bs-target="#modalHapusAkun">
                            <i class="bi bi-trash fs-5" style="width: 28px;"></i> Hapus Akun Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('dashboardContent').innerHTML = html;
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

// Handler form reset akun
const formResetAkun = document.getElementById('formResetAkun');
if (formResetAkun) {
    formResetAkun.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = formResetAkun.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
        
        const formData = new FormData(formResetAkun);
        
        try {
            const response = await fetch('../../backend_api/pemilik/update_akun.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('modalResetAkun')).hide();
                showToast(result.message);
                formResetAkun.reset();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

// Handler form hapus akun
const formHapusAkun = document.getElementById('formHapusAkun');
if (formHapusAkun) {
    formHapusAkun.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = formHapusAkun.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menghapus...';
        
        const formData = new FormData(formHapusAkun);
        
        try {
            const response = await fetch('../../backend_api/pemilik/hapus_akun.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                window.location.href = '../login.php?msg=akun_dihapus';
            } else {
                showToast(result.message, 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `position-fixed bottom-0 end-0 p-3 m-3 rounded-3 shadow text-white ${type === 'success' ? 'bg-success' : 'bg-danger'}`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
<script>
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileSidebar = document.getElementById('mobileSidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        mobileSidebar.classList.toggle('show-sidebar');
        sidebarOverlay.classList.toggle('show-overlay');
    });
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
        mobileSidebar.classList.remove('show-sidebar');
        sidebarOverlay.classList.remove('show-overlay');
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