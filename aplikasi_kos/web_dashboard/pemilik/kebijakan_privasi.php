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
    <title>Kebijakan Privasi - MyKos</title>
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

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div id="policyContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat kebijakan privasi...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
async function loadPolicy() {
    try {
        const response = await fetch('../../backend_api/kebijakan_privasi/get_kebijakan.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            renderPolicy(result);
        } else {
            document.getElementById('policyContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('policyContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderPolicy(data) {
    const introText = data.intro_text || '';
    const sections = data.data || [];
    
    let introHtml = '';
    if (introText) {
        introHtml = `<div class="alert alert-info border-0 shadow-sm mb-4">${introText}</div>`;
    }
    
    let sectionsHtml = '';
    sections.forEach(section => {
        // Skip intro section (id=1) jika tidak punya judul
        if (section.id === 1 && !section.judul_section) return;
        
        sectionsHtml += `
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="section-title mb-3">${escapeHtml(section.judul_section)}</h5>
                    <div class="policy-section-body">
                        ${section.isi_konten || '<p class="text-muted">Belum ada konten.</p>'}
                    </div>
                </div>
            </div>
        `;
    });
    
    const html = `
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-0"><i text-primary me-2"></i>Kebijakan Privasi</h2>
                    <p class="text-muted">Aplikasi My Kos (Icarus Developer)</p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-4">
                ${introHtml}
                ${sectionsHtml || '<p class="text-muted">Belum ada kebijakan privasi.</p>'}
                
                <div class="mt-4 pt-3 border-top text-end">
                    <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-printer me-2"></i>Cetak Dokumen
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('policyContent').innerHTML = html;
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

document.addEventListener('DOMContentLoaded', loadPolicy);
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