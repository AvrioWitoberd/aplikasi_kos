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
    <title>Tambah Kos - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
            <div class="form-card-tambah">
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div>
                            <h3 class="fw-bold text-dark mb-0">Tambah Unit Kos Baru</h3>
                            <p class="text-muted">Lengkapi data kos anda untuk menarik minat pencari.</p>
                        </div>
                    </div>
                </div>

                <form id="formTambahKos" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label class="form-label fw-bold">Nama Kos</label>
                            <input type="text" name="nama_kos" class="form-control" placeholder="Contoh: Kos Bang Rio" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Sisa Kamar (Stok)</label>
                            <input type="number" name="jumlah_kamar" class="form-control" min="0" placeholder="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Tipe Kos</label>
                            <select name="tipe_kos" class="form-select" required>
                                <option value="putra">Putra</option>
                                <option value="putri">Putri</option>
                                <option value="campur">Campur</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Kota</label>
                            <input type="text" name="kota" class="form-control" placeholder="Contoh: Kota Malang" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Harga Per Bulan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_per_bulan" class="form-control" min="0" placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Deskripsi Kos</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan kelebihan kos Anda..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" class="form-control" rows="2" placeholder="Sebutkan jalan, nomor, dan patokan..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Fasilitas Kos</label>
                        <input type="text" name="fasilitas_utama" class="form-control" placeholder="WiFi, AC, Kamar Mandi Dalam, Kasur">
                        <small class="text-muted">(Gunakan koma sebagai pemisah)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Nomor Telepon / WhatsApp Kos</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="no_hp_kos" class="form-control" placeholder="0812xxxxxx" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Link Google Maps (Lokasi Kos)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="url" name="link_maps" class="form-control" placeholder="https://maps.app.goo.gl/..." required>
                        </div>
                        <small class="text-muted">Pastikan link diawali dengan http:// atau https://</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Peraturan Kos</label>
                        <textarea name="peraturan_kos" id="peraturan_kos" class="form-control" rows="4" placeholder="1. Jam malam 22.00&#10;2. Dilarang membawa peliharaan..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Area Sekitar Kos</label>
                        <textarea name="area_sekitar_kos" id="area_sekitar_kos" class="form-control" rows="4" placeholder="1. Kampus UB 5 menit&#10;2. Kampus Polinema 4 menit..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" id="uploadLabel">Foto Unit (Maks 2MB per foto)</label>
                        <div class="upload-box-tambah" id="drop-area">
                            <i class="bi bi-images fs-2 text-primary"></i>
                            <p class="mb-0 mt-2">Klik atau Seret Foto ke Sini</p>
                            <small class="text-muted">Maksimal 5 foto</small>
                            <input type="file" id="fileInput" class="d-none" multiple accept="image/jpeg,image/png,image/jpg">
                        </div>
                        <div id="preview-container" class="row g-3 mt-3"></div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" id="btnSubmit" class="btn btn-primary py-3 fw-bold rounded-3">Simpan Data Kos</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="modalSukses" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Berhasil!</h5>
                <p class="text-muted small">Data kos berhasil disimpan.</p>
                <a href="kelola_kos.php" class="btn btn-primary rounded-pill px-4 mt-2">Lihat Daftar Kos Saya</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let selectedFiles = [];

// Preview multiple images
function previewImages(files) {
    const container = document.getElementById('preview-container');
    const maxSize = 2 * 1024 * 1024;
    
    Array.from(files).forEach((file) => {
        if (file.size > maxSize) {
            alert(`File ${file.name} terlalu besar! Maks 2MB`);
            return;
        }
        selectedFiles.push(file);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-auto preview-card-tambah';
            div.setAttribute('data-index', selectedFiles.length - 1);
            div.innerHTML = `
                <img src="${e.target.result}">
                <button type="button" class="btn-delete-preview-tambah" onclick="removeImage(${selectedFiles.length - 1})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    renderPreview();
}

function renderPreview() {
    const container = document.getElementById('preview-container');
    container.innerHTML = "";
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-auto preview-card-tambah';
            div.innerHTML = `
                <img src="${e.target.result}">
                <button type="button" class="btn-delete-preview-tambah" onclick="removeImage(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

// Upload box click
document.getElementById('drop-area').addEventListener('click', () => {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', (e) => {
    previewImages(e.target.files);
    e.target.value = '';
});

// Submit form
const form = document.getElementById('formTambahKos');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
    
    const formData = new FormData(form);
    formData.delete('foto_kos[]');
    selectedFiles.forEach(file => {
        formData.append('foto_kos[]', file);
    });
    
    try {
        const response = await fetch('../../backend_api/pemilik/tambah_kos.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            const modal = new bootstrap.Modal(document.getElementById('modalSukses'));
            modal.show();
            form.reset();
            selectedFiles = [];
            renderPreview();
        } else {
            alert('Gagal: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan sistem');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
});

// Fungsi untuk inisialisasi textarea dengan nomor urut
function initNumberedTextarea(textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    
    textarea.addEventListener('focus', function() {
        if (this.value.trim() === '') {
            this.value = '1. ';
        }
    });
    
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const pos = this.selectionStart;
            const value = this.value;
            const textBeforeCursor = value.substring(0, pos);
            const lines = textBeforeCursor.split('\n');
            const currentLine = lines[lines.length - 1];
            
            // Cek apakah baris saat ini memiliki pola nomor (contoh: "1. ")
            const match = currentLine.match(/^(\d+)\.\s/);
            
            if (match) {
                e.preventDefault();
                const nextNum = parseInt(match[1]) + 1;
                const insertText = '\n' + nextNum + '. ';
                this.value = textBeforeCursor + insertText + value.substring(pos);
                this.setSelectionRange(pos + insertText.length, pos + insertText.length);
            }
        }
    });
}

// Inisialisasi kedua textarea
initNumberedTextarea('peraturan_kos');
initNumberedTextarea('area_sekitar_kos');

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