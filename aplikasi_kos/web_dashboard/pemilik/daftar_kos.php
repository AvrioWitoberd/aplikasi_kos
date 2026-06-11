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
    <title>Daftar Kos - MyKos Admin</title>
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
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Daftar Kos</h2>
                        <p class="text-muted">Rekomendasi pilihan kos terbaik untuk anda.</p>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Total Unit Terdaftar</span>
                        <h4 class="fw-bold text-primary mb-0" id="totalKos">0</h4>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari lokasi, kota, atau nama kos...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filterTipe" class="form-select filter-select">
                        <option value="">Semua Tipe</option>
                        <option value="putra">Putra</option>
                        <option value="putri">Putri</option>
                        <option value="campur">Campur</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterHarga" class="form-select filter-select">
                        <option value="0-999999999">Semua Harga</option>
                        <option value="0-500000">Dibawah 500.000</option>
                        <option value="500000-1000000">500000 - 1 juta</option>
                        <option value="1000000-2000000">1 Juta - 2 Juta</option>
                        <option value="2000000-999999999">Diatas 2 Juta</option>
                    </select>
                </div>
            </div>

            <!-- Area Kartu Kos -->
            <div id="kosContainer" class="kos-grid">
                <div class="text-center p-5 w-100">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data kos...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const kosContainer = document.getElementById('kosContainer');
    const totalKosText = document.getElementById('totalKos');

    async function loadDataKos() {
        const search = document.getElementById('searchInput').value;
        const tipe   = document.getElementById('filterTipe').value;
        const hargaVal = document.getElementById('filterHarga').value;
        const [min, max] = hargaVal.split('-');

        // Loading state
        kosContainer.innerHTML = `
            <div class="text-center p-5 w-100">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Menghubungkan ke server...</p>
            </div>`;

        try {

            const response = await fetch(`../../backend_api/kos/get_semua_kos.php?search=${encodeURIComponent(search)}&tipe=${tipe}&min_harga=${min}&max_harga=${max}`);
            
            if (!response.ok) throw new Error("HTTP Error: " + response.status);
            
            const result = await response.json();
            console.log("Data diterima:", result); // Cek data di Console F12

            if (result.status === 'success') {
                totalKosText.innerText = `${result.total} Kos`;
                renderCards(result.data);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error("Detail Error:", error);
            kosContainer.innerHTML = `
                <div class="alert alert-danger w-100">
                    <i class="bi bi-exclamation-triangle"></i> Gagal memuat data: ${error.message}
                </div>`;
        }
    }

    // Fungsi pembantu untuk membuat deretan bintang
    function generateStars(rating) {
        let starsHtml = '';
        const fullStars = Math.floor(rating); // Bintang penuh
        const hasHalfStar = rating % 1 >= 0.5; // Cek apakah butuh bintang setengah
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0); // Sisa bintang kosong

        // Tambah bintang penuh
        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="bi bi-star-fill text-warning me-1"></i>';
        }
        // Tambah bintang setengah (jika rating 3.5, 4.5, dll)
        if (hasHalfStar) {
            starsHtml += '<i class="bi bi-star-half text-warning me-1"></i>';
        }
        // Tambah bintang kosong
        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="bi bi-star text-muted me-1"></i>';
        }
        return starsHtml;
    }

    function renderCards(data) {
        if (data.length === 0) {
            kosContainer.style.display = 'flex';
            kosContainer.innerHTML = `<div class="text-center p-5 w-100"><h5>Kos tidak ditemukan</h5></div>`;
            return;
        }
        
        kosContainer.style.display = 'grid';
        let html = '';
        
        data.forEach(kos => {
            const hargaFmt = new Intl.NumberFormat('id-ID', { 
                style: 'currency', currency: 'IDR', maximumFractionDigits: 0 
            }).format(kos.harga_per_bulan);
            
            const foto = kos.foto_utama ? `../../uploads/foto_kos/${kos.foto_utama}` : 'https://placehold.co/400x300?text=No+Image';
            
            // Hitung Rating
            const ratingValue = parseFloat(kos.rata_rating) || 0;
            const percent = (ratingValue / 5) * 100; // Hitung persen untuk isi bintang

            let statusBadge = parseInt(kos.jumlah_kamar) > 0 
                ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Tersedia</span>`
                : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tidak Tersedia</span>`;

                html += `
                <div class="card-kos shadow-sm border-0 rounded-4 overflow-hidden h-100" 
                    onclick="window.location.href='detail_kos.php?id=${kos.id_kos}'">
                    
                    <img src="${foto}" alt="${kos.nama_kos}" style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-truncate" style="max-width: 65%;">${kos.nama_kos}</h6>
                            <span class="badge bg-info-subtle text-info text-capitalize border border-info-subtle">
                                ${kos.tipe_kos}
                            </span>
                        </div>

                        <!-- BARIS LOKASI (ALAMAT LENGKAP) -->
                        <div class="info-row">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${kos.alamat_lengkap || ''}">
                                ${kos.alamat_lengkap || '-'}
                            </span>
                        </div>

                        <!-- BARIS RATING (Ukuran & Jarak Sama dengan Lokasi) -->
                        <div class="info-row mb-3">
                            <div class="star-container">
                                <i class="bi bi-star-fill"></i>
                                <div class="star-fill" style="--rating-width: ${percent}%">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                            <span class="info-rating-number">${ratingValue.toFixed(1)}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <span class="text-primary fw-bold" style="font-size: 1.1rem;">${hargaFmt}</span>
                                <small class="text-muted">/bln</small>
                            </div>
                            ${statusBadge}
                        </div>
                    </div>
                </div>
            `;
        });
        
        kosContainer.innerHTML = html;
    }

    // Debounce search agar tidak terlalu berat membebani database
    let timeout = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(loadDataKos, 500);
    });

    document.getElementById('filterTipe').addEventListener('change', loadDataKos);
    document.getElementById('filterHarga').addEventListener('change', loadDataKos);

    window.onload = loadDataKos;
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