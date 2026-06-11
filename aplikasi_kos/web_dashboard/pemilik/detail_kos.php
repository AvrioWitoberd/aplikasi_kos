<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../login.php");
    exit;
}

$id_kos = $_GET['id'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Kos - MyKos Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <div class="container py-4" id="detailContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data kos...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const idKos = "<?= $id_kos ?>";

async function loadDetail() {
    try {
        const response = await fetch(`../../backend_api/kos/get_detail_kos.php?id=${idKos}`);
        const result = await response.json();

        if (result.status === 'success') {
            renderDetail(result.data);
        } else {
            document.getElementById('detailContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('detailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data. Periksa koneksi API.</div>';
    }
}

function renderDetail(data) {
    const kos = data.kos;
    const rating = data.rating;
    const fotos = data.fotos || [];
    const fasilitas = data.fasilitas || [];
    const maps = data.maps || {};

    // Bersihkan nomor WA
    let waNumber = kos.no_hp_pemilik || '';
    waNumber = waNumber.replace(/[^0-9]/g, '');
    if (waNumber.startsWith('0')) {
        waNumber = '62' + waNumber.substring(1);
    }

    let html = `
        <!-- Carousel Foto -->
        <div id="kosCarousel" class="carousel slide mb-4 rounded-4 overflow-hidden shadow-sm">
            <div class="carousel-inner">
                ${fotos.length > 0 ? fotos.map((foto, index) => `
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <img src="../../uploads/foto_kos/${escapeHtml(foto)}" class="d-block w-100" alt="Foto Kos">
                    </div>
                `).join('') : `
                    <div class="carousel-item active">
                        <img src="https://placehold.co/1200x400?text=No+Image" class="d-block w-100" alt="No Image">
                    </div>
                `}
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#kosCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#kosCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <h2 class="fw-bold">${escapeHtml(kos.nama_kos)}</h2>
                <p class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> ${escapeHtml(kos.alamat_lengkap || '')}, ${escapeHtml(kos.kota || '')}</p>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex gap-3">
                        <span class="badge bg-light text-dark border p-2"><i class="bi bi-gender-ambiguous me-1"></i> Kos ${escapeHtml(kos.tipe_kos || 'Campur')}</span>
                        <span class="badge bg-light text-dark border p-2">
                            <i class="bi bi-star-fill text-warning me-1"></i> 
                            ${rating.rata_rating || '0.0'} 
                            <span class="text-muted ms-1" style="font-size: 0.7rem;">(${rating.total_ulasan || 0} ulasan)</span>
                        </span>
                        <span class="badge bg-light text-dark border p-2"><i class="bi bi-door-open me-1"></i> ${kos.jumlah_kamar || 0} Tersedia</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnSimpan" class="btn-action-outline" onclick="toggleFavorit(${kos.id_kos})">
                            <i id="iconSimpan" class="bi bi-heart"></i> <span id="textSimpan">Simpan</span>
                        </button>
                        <button class="btn-action-outline" onclick="bagikanKos('${escapeHtml(kos.nama_kos)}', '${kos.id_kos}')">
                            <i class="bi bi-share"></i> Bagikan
                        </button>
                    </div>
                </div>

                <hr>

                <!-- DESKRIPSI -->
                <div class="mt-5">
                    <h5 class="fw-bold">Deskripsi Kos</h5>
                    <p class="text-secondary">${nl2br(escapeHtml(kos.deskripsi || 'Tidak ada deskripsi.'))}</p>
                </div>

                <!-- FASILITAS -->
                ${fasilitas.length > 0 && fasilitas[0] ? `
                <div class="mt-5 detail-border-bottom">
                    <h5 class="fw-bold mb-4">Fasilitas Kos</h5>
                    <div class="d-flex flex-wrap gap-3">
                        ${fasilitas.filter(f => f && f.trim()).map(f => `
                        <span class="badge bg-light text-secondary border rounded-pill py-3 px-4 fs-6 fw-normal" style="font-size: 0.95rem !important;">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            ${escapeHtml(f.trim())}
                        </span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- PERATURAN -->
                <div class="mt-5">
                    <h5 class="fw-bold">Peraturan Kos</h5>
                    <div class="p-3 rounded-4 border bg-light">
                        <p class="text-secondary mb-0">
                            ${kos.peraturan_kos ? nl2br(escapeHtml(kos.peraturan_kos)) : '<i class="bi bi-info-circle me-1"></i> Belum ada peraturan yang ditambahkan.'}
                        </p>
                    </div>
                </div>

                <!-- AREA SEKITAR -->
                <div class="mt-5">
                    <h5 class="fw-bold">Area Sekitar Kos</h5>
                    <div class="p-3 rounded-4 border bg-light">
                        <p class="text-secondary mb-0">
                            ${kos.area_sekitar_kos ? nl2br(escapeHtml(kos.area_sekitar_kos)) : '<i class="bi bi-info-circle me-1"></i> Belum ada informasi area sekitar.'}
                        </p>
                    </div>
                </div>

                <!-- LOKASI MAPS -->
                <div class="mt-5 maps-section">
                    <h5 class="fw-bold"><i class="bi bi-map-fill me-2"></i> Lokasi di Maps</h5>
                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> ${escapeHtml(kos.alamat_lengkap || '')}, ${escapeHtml(kos.kota || '')}</p>
                    <div class="map-container">
                        ${maps.embed_url ? `
                            <iframe src="${maps.embed_url}" width="100%" height="100%" frameborder="0" style="border:0; border-radius: 16px;" allowfullscreen loading="lazy"></iframe>
                        ` : `
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px; border-radius: 16px;">
                                <p class="text-muted">Maps tidak tersedia. Pemilik belum menyertakan link maps yang valid.</p>
                            </div>
                        `}
                    </div>
                    <div class="maps-button-wrapper">
                        ${maps.direct_url && maps.direct_url != '#' ? `
                            <a href="${maps.direct_url}" target="_blank" class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-pin-map-fill me-2"></i> Buka di Google Maps
                            </a>
                        ` : `
                            <button class="btn btn-secondary rounded-pill px-4" disabled>
                                <i class="bi bi-pin-map-fill me-2"></i> Link Maps Tidak Tersedia
                            </button>
                        `}
                    </div>
                </div>

                <!-- RATING SECTION -->
                <div class="rating-box mb-4 rating-section">
                    <p class="fw-bold mb-3">Berikan Rating untuk Kos Ini</p>
                    <div class="star-rating d-flex justify-content-center gap-3 mb-3" id="starRating">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                    <button id="btnKirimRating" class="btn btn-pesan px-5" onclick="kirimRating()">Kirim Rating</button>
                    <div id="ratingStatus" class="mt-3 small text-muted"></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-price-card" style="top: 20px; border-radius: 15px;">
                    <p class="text-muted mb-1">Harga Kos Per-Bulan</p>
                    <h3 class="text-warning fw-bold mb-4">Rp. ${new Intl.NumberFormat('id-ID').format(kos.harga_per_bulan || 0)}</h3>
                    
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/${waNumber}?text=Halo, saya tertarik dengan kos ${encodeURIComponent(kos.nama_kos || '')}" target="_blank" class="btn btn-pesan py-3 rounded-pill">
                            <i class="bi bi-whatsapp me-2"></i> Pesan Sekarang
                        </a>
                        <a href="https://wa.me/${waNumber}" target="_blank" class="btn btn-outline-secondary py-3 rounded-pill">
                            <i class="bi bi-chat-dots me-2"></i> Hubungi Pemilik
                        </a>
                    </div>
                </div>
            </div>

            <!-- MOBILE FIXED BOTTOM BAR -->
            <div class="fixed-bottom-mobile d-lg-none shadow-lg border-top">
                <div class="d-flex align-items-center justify-content-between p-3 gap-3">

                    <div>
                        <small class="text-muted d-block">Harga / Bulan</small>
                        <h5 class="fw-bold text-warning mb-0">
                            Rp. ${new Intl.NumberFormat('id-ID').format(kos.harga_per_bulan || 0)}
                        </h5>
                    </div>

                    <a href="https://wa.me/${waNumber}?text=Halo, saya tertarik dengan kos ${encodeURIComponent(kos.nama_kos || '')}" 
                    target="_blank"
                    class="btn btn-pesan rounded-pill px-4 btn-pesan-mobile">
                        <i class="bi bi-whatsapp me-2"></i>
                        Pesan Sekarang
                    </a>

                </div>
            </div>
        </div>
    </div>
    `;

    document.getElementById('detailContent').innerHTML = html;
    
    // Inisialisasi carousel
    const carouselElement = document.getElementById('kosCarousel');
    if (carouselElement) {
        new bootstrap.Carousel(carouselElement, { ride: false });
    }
    cekStatusFavorit(kos.id_kos);
    initStarRating();
    cekUserRating();
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

// Cek status favorit saat halaman dimuat
async function cekStatusFavorit(idKos) {
    try {
        const response = await fetch(`../../backend_api/favorit/cek_status.php?id=${idKos}`);
        const result = await response.json();
        
        if (result.status === 'success' && result.is_favorit) {
            const icon = document.getElementById('iconSimpan');
            const text = document.getElementById('textSimpan');
            if (icon) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
            }
            if (text) text.innerText = 'Disimpan';
        }
    } catch (error) {
        console.error('Error cek favorit:', error);
    }
}

// Toggle favorit (Simpan/Hapus)
async function toggleFavorit(idKos) {
    const formData = new FormData();
    formData.append('id_kos', idKos);
    
    try {
        const response = await fetch('../../backend_api/favorit/tambah.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        const icon = document.getElementById('iconSimpan');
        const text = document.getElementById('textSimpan');
        
        if (result.action === 'added') {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill', 'text-danger');
            text.innerText = 'Disimpan';
            showToast(result.message, 'success'); // Hijau untuk save
        } else if (result.action === 'removed') {
            icon.classList.remove('bi-heart-fill', 'text-danger');
            icon.classList.add('bi-heart');
            text.innerText = 'Simpan';
            showToast(result.message, 'error'); // Merah untuk unsave
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Gagal menyimpan', 'error');
    }
}

// Fungsi Bagikan
function bagikanKos(namaKos, idKos) {
    const url = window.location.href.split('?')[0] + `?id=${idKos}`;
    
    if (navigator.share) {
        // Web Share API (mobile & desktop support)
        navigator.share({
            title: namaKos,
            text: `Lihat kos ${namaKos} di MyKos!`,
            url: url
        }).catch(err => console.log('Share cancelled:', err));
    } else {
        // Fallback: copy ke clipboard
        navigator.clipboard.writeText(url);
        showToast('Link berhasil disalin!');
    }
}

// Fungsi Toast Notifikasi sederhana
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    let bgColor = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#ffc107');
    let icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-triangle' : 'info-circle');
    
    toast.className = `position-fixed bottom-0 end-0 p-3 m-3 rounded-3 shadow text-white`;
    toast.style.backgroundColor = bgColor;
    toast.style.zIndex = '9999';
    toast.style.minWidth = '250px';
    toast.innerHTML = `<i class="bi bi-${icon} me-2"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Variabel untuk menyimpan rating yang dipilih
let selectedRating = 0;
let hasRated = false;

// Fungsi untuk cek rating user sebelumnya
async function cekUserRating() {
    try {
        const response = await fetch(`../../backend_api/rating/cek_rating.php?id=${idKos}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            if (result.user_rating) {
                hasRated = true;
                selectedRating = result.user_rating;
                updateStarDisplay(selectedRating);
                document.getElementById('ratingStatus').innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Terima kasih atas rating yang Anda berikan!';
                document.getElementById('btnKirimRating').disabled = true;
                document.getElementById('btnKirimRating').style.opacity = '0.6';
            }
        }
    } catch (error) {
        console.error('Error cek rating:', error);
    }
}

// Fungsi update tampilan bintang
function updateStarDisplay(rating) {
    const stars = document.querySelectorAll('#starRating i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('bi-star');
            star.classList.add('bi-star-fill');
            star.style.color = '#ffc107';
        } else {
            star.classList.remove('bi-star-fill');
            star.classList.add('bi-star');
            star.style.color = '#ddd';
        }
    });
}

// Event listener untuk bintang rating
function initStarRating() {
    const stars = document.querySelectorAll('#starRating i');
    
    stars.forEach(star => {
        // Hover effect
        star.addEventListener('mouseenter', function() {
            if (hasRated) return;
            const value = parseInt(this.dataset.value);
            stars.forEach((s, idx) => {
                if (idx < value) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                    s.style.color = '#ffc107';
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                    s.style.color = '#ddd';
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            if (hasRated) return;
            stars.forEach(s => {
                s.style.color = '#ddd';
            });
            updateStarDisplay(selectedRating);
        });
        
        // Click to rate
        star.addEventListener('click', function() {
            if (hasRated) {
                showToast('Anda sudah memberikan rating sebelumnya!', 'info');
                return;
            }
            selectedRating = parseInt(this.dataset.value);
            updateStarDisplay(selectedRating);
        });
    });
}

// Fungsi kirim rating
async function kirimRating() {
    if (hasRated) {
        showToast('Anda sudah memberikan rating sebelumnya!', 'info');
        return;
    }
    
    if (selectedRating === 0) {
        showToast('Silakan pilih rating bintang terlebih dahulu!', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('id_kos', idKos);
    formData.append('skor', selectedRating);
    
    try {
        const response = await fetch('../../backend_api/rating/tambah_rating.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {

            hasRated = true;

            document.getElementById('btnKirimRating').disabled = true;

            document.getElementById('btnKirimRating').style.opacity = '0.6';

            document.getElementById('ratingStatus').innerHTML =
                '<i class="bi bi-check-circle-fill text-success me-1"></i> Terima kasih atas rating yang Anda berikan!';

            showRatingThankYouModal(idKos);

        }

        // 🔥 BELUM LOGIN
        else if (result.status === 'login_required') {

            showToast('Harus login terlebih dahulu', 'warning');

            setTimeout(() => {
                window.location.href = '../login.php';
            }, 1500);

        }

        // 🔥 ADMIN / PEMILIK
        else if (result.status === 'forbidden') {

            showToast(result.message, 'warning');

        }

        // 🔥 ERROR LAIN
        else {

            showToast(result.message || 'Gagal mengirim rating', 'error');

        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Gagal mengirim rating', 'error');
    }
}

// Modal ucapan terima kasih
function showRatingThankYouModal(namaKos) {
    // Buat modal
    const modalHtml = `
        <div class="modal fade" id="thankYouModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                    <div class="modal-body text-center p-4">
                        <div class="text-success mb-3">
                            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Terima Kasih!</h5>
                        <p class="text-muted small mb-0">Terima kasih telah memberikan rating pada kos ini.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4 mt-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Hapus modal lama jika ada
    const oldModal = document.getElementById('thankYouModal');
    if (oldModal) oldModal.remove();
    
    // Tambahkan modal ke body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('thankYouModal'));
    modal.show();
    
    // Hapus modal setelah ditutup
    document.getElementById('thankYouModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

document.addEventListener('DOMContentLoaded', loadDetail);
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