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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kos - MyKos</title>
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
            <div id="editContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data kos...</p>
                </div>
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
                <p class="text-muted small">Data kos berhasil diperbarui.</p>
                <a href="kelola_kos.php" class="btn btn-primary rounded-pill px-4 mt-2">Lihat Daftar Kos</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const idKos = "<?= $id_kos ?>";
let fotoToDelete = [];

async function loadData() {
    try {
        const response = await fetch(`../../backend_api/pemilik/edit_kos.php?id=${idKos}`);
        const result = await response.json();
        
        if (result.status === 'success') {
            renderForm(result.data, result.fotos);
        } else {
            document.getElementById('editContent').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('editContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderForm(kos, fotos) {
    // Tampilkan foto yang sudah ada
    let existingPhotosHtml = '';
    if (fotos && fotos.length > 0) {
        existingPhotosHtml = `
            <div class="mb-4">
                <label class="form-label fw-bold">Foto Kos Saat Ini</label>
                <div class="d-flex flex-wrap gap-3" id="existingPhotosContainer">
                    ${fotos.map((foto, index) => `
                        <div class="preview-card-tambah position-relative" data-foto="${foto}">
                            <img src="../../uploads/foto_kos/${foto}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                            <button type="button" class="btn-delete-preview-tambah" onclick="markFotoForDeletion('${foto}', this)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `).join('')}
                </div>
                <small class="text-muted">Foto yang sudah ada. Klik × untuk menghapus (setelah disimpan akan terhapus).</small>
            </div>
            <input type="hidden" name="fotos_to_delete" id="fotosToDelete" value="">
        `;
    }
    
    const html = `
        <div class="form-card-tambah">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <a href="kelola_kos.php" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="fw-bold text-dark mb-0">Edit Unit Kos</h3>
                        <p class="text-muted">Perbarui data kos anda.</p>
                    </div>
                </div>
            </div>

            <form id="formEditKos" enctype="multipart/form-data">
                <input type="hidden" name="id_kos" value="${kos.id_kos}">
                
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <label class="form-label fw-bold">Nama Kos</label>
                        <input type="text" name="nama_kos" class="form-control" value="${escapeHtml(kos.nama_kos)}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Sisa Kamar (Stok)</label>
                        <input type="number" name="jumlah_kamar" class="form-control" min="0" value="${kos.jumlah_kamar}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Tipe Kos</label>
                        <select name="tipe_kos" class="form-select" required>
                            <option value="putra" ${kos.tipe_kos === 'putra' ? 'selected' : ''}>Putra</option>
                            <option value="putri" ${kos.tipe_kos === 'putri' ? 'selected' : ''}>Putri</option>
                            <option value="campur" ${kos.tipe_kos === 'campur' ? 'selected' : ''}>Campur</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Kota</label>
                        <input type="text" name="kota" class="form-control" value="${escapeHtml(kos.kota)}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label fw-bold">Harga Per Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_per_bulan" class="form-control" min="0" value="${kos.harga_per_bulan}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Deskripsi Kos</label>
                    <textarea name="deskripsi" class="form-control" rows="3">${escapeHtml(kos.deskripsi || '')}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-control" rows="2" required>${escapeHtml(kos.alamat_lengkap)}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Fasilitas Kos</label>
                    <input type="text" name="fasilitas_utama" class="form-control" value="${escapeHtml(kos.fasilitas_utama || '')}" placeholder="WiFi, AC, Kamar Mandi Dalam, Kasur">
                    <small class="text-muted">(Gunakan koma sebagai pemisah)</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">Nomor Telepon / WhatsApp Kos</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="no_hp_kos" class="form-control" value="${escapeHtml(kos.no_hp_kos || '')}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Link Google Maps</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-danger"><i class="bi bi-geo-alt-fill"></i></span>
                        <input type="url" name="link_maps" class="form-control" value="${escapeHtml(kos.link_maps || '')}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Peraturan Kos</label>
                    <textarea name="peraturan_kos" id="peraturan_kos" class="form-control" rows="4">${escapeHtml(kos.peraturan_kos || '')}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Area Sekitar Kos</label>
                    <textarea name="area_sekitar_kos" id="area_sekitar_kos" class="form-control" rows="4">${escapeHtml(kos.area_sekitar_kos || '')}</textarea>
                </div>

                <!-- ========== BAGIAN FOTO (DITAMBAHKAN DI SINI) ========== -->
                ${existingPhotosHtml}

                <div class="mb-4">
                    <label class="form-label fw-bold">Tambah Foto Baru (Opsional)</label>
                    <div class="upload-box-tambah" id="drop-area">
                        <i class="bi bi-images fs-2 text-primary"></i>
                        <p class="mb-0 mt-2">Klik atau Seret Foto ke Sini</p>
                        <small class="text-muted">Maksimal 5 foto, format JPG/PNG, max 2MB</small>
                        <input type="file" id="fileInput" class="d-none" multiple accept="image/jpeg,image/png,image/jpg">
                    </div>
                    <div id="preview-container" class="row g-3 mt-3"></div>
                </div>
                <!-- ========== END BAGIAN FOTO ========== -->

                <div class="d-grid mt-5">
                    <button type="submit" id="btnSubmit" class="btn btn-primary py-3 fw-bold rounded-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    `;
    
    document.getElementById('editContent').innerHTML = html;
    
    // Inisialisasi upload foto baru
    let selectedFiles = [];
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('preview-container');
    
    if (dropArea) {
        dropArea.addEventListener('click', () => fileInput.click());
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            const maxSize = 2 * 1024 * 1024;
            
            files.forEach((file) => {
                if (file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar! Maks 2MB`);
                    return;
                }
                selectedFiles.push(file);
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-auto preview-card-tambah';
                    div.innerHTML = `
                        <img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                        <button type="button" class="btn-delete-preview-tambah" onclick="this.parentElement.remove(); selectedFiles = selectedFiles.filter(f => f !== file);">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
            fileInput.value = '';
        });
    }
    
    // Update submit untuk menyertakan file
    const form = document.getElementById('formEditKos');
    const originalSubmit = form.onsubmit;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSubmit');
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
        
        const formData = new FormData(form);
        
        // Tambahkan foto baru
        selectedFiles.forEach(file => {
            formData.append('foto_kos[]', file);
        });
        
        try {
            const response = await fetch('../../backend_api/pemilik/update_kos.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                const modal = new bootstrap.Modal(document.getElementById('modalSukses'));
                modal.show();
                setTimeout(() => {
                    window.location.href = 'kelola_kos.php';
                }, 1500);
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
    
    // Inisialisasi textarea dengan nomor urut
    initNumberedTextarea('peraturan_kos');
    initNumberedTextarea('area_sekitar_kos');
}

function initNumberedTextarea(textareaId) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const pos = this.selectionStart;
            const value = this.value;
            const textBeforeCursor = value.substring(0, pos);
            const lines = textBeforeCursor.split('\n');
            const currentLine = lines[lines.length - 1];
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

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function markFotoForDeletion(foto, buttonElement) {
    fotoToDelete.push(foto);
    buttonElement.closest('.preview-card-tambah').remove();
    const inputHidden = document.getElementById('fotosToDelete');
    if (inputHidden) {
        inputHidden.value = fotoToDelete.join(',');
    }
}

document.addEventListener('DOMContentLoaded', loadData);
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