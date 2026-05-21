<?php
require_once '../../config/auth_check.php'; 
require_once '../../config/database.php';
cekAkses('pemilik');
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
    <!-- <link rel="stylesheet" href="style_admin.css"> -->
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        .preview-card { position: relative; width: 120px; height: 120px; }
        .preview-card img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; border: 1px solid #ddd; }
        .btn-delete-preview { 
            position: absolute; top: -5px; right: -5px; 
            background: red; color: white; border: none; 
            border-radius: 50%; width: 25px; height: 25px; 
            font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .upload-box {
            border: 2px dashed #ccc;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
        }

        .form-control::placeholder,
        .form-select::placeholder {
            color: #b9b9b9 !important;
            opacity: 1; 
            font-weight: 300;
        }
                .upload-box:hover { border-color: #0d6efd; background: #f8f9fa; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <!-- <div class="row justify-content-center"> -->
        <!-- <div class="col-lg-10"> -->
            <div class="card shadow-sm p-4">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark">Tambah Unit Kos Baru</h3>
                    <p class="text-muted">Lengkapi data kos anda untuk menarik minat pencari.</p>
                </div>

                <form action="proses_tambah_kos.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label class="form-label">Nama Kos</label>
                            <input type="text" name="nama_kos" class="form-control" placeholder="Contoh: Kos Bang Rio" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Sisa Kamar (Stok)</label>
                            <input type="number" name="jumlah_kamar" class="form-control" 
                                min="0" step="1" 
                                oninput="this.value = Math.abs(Math.round(this.value))" 
                                placeholder="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Tipe Kos</label>
                            <select name="tipe_kos" class="form-select" required>
                                <option value="putra">Putra</option>
                                <option value="putri">Putri</option>
                                <option value="campur">Campur</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Kota</label>
                            <input type="text" name="kota" class="form-control" placeholder="Contoh: Kota Malang" required>
                        </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Harga Per Bulan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_per_bulan" class="form-control" 
                                        min="0" step="1" 
                                        oninput="this.value = Math.abs(Math.round(this.value))" 
                                        placeholder="0" required>
                                </div>
                            </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Deskripsi Kos</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan kelebihan kos Anda..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" class="form-control" rows="2" placeholder="Sebutkan jalan, nomor, dan patokan..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Fasilitas Kos</label>
                        <input type="text" name="fasilitas_utama" class="form-control" placeholder="WiFi, AC, Kamar Mandi Dalam, Kasur">
                        <small class="text-muted">(Gunakan koma sebagai pemisah)</small>
                    </div>

                    <div class="mb-4">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Nomor Telepon / WhatsApp Kos</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="no_hp_kos" class="form-control" placeholder="0812xxxxxx" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Link Google Maps (Lokasi Kos)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="url" name="link_maps" class="form-control" 
                                placeholder="Buka Google Maps > Share > Copy Link (https://maps.app.goo.gl/...)" required>
                        </div>
                        <small class="text-muted">Pastikan link diawali dengan http:// atau https://</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Peraturan Kos</label>
                        <textarea id="peraturan_kos" name="peraturan_kos" class="form-control" rows="5" 
                                placeholder="1. Jam malam 22.00&#10;2. Dilarang membawa peliharaan..."
                                onfocus="initList(this)"></textarea>
                        <small class="text-muted">Tekan <b>Enter</b> untuk menambah baris peraturan baru secara otomatis.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Area Sekitar Kos</label>
                        <textarea id="area_sekitar_kos" name="area_sekitar_kos" class="form-control" rows="5" 
                                placeholder="1. Kampus UB 5menit&#10;2. Kampus polinema 4menit..."
                                onfocus="initList(this)"></textarea>
                        <small class="text-muted">Tekan <b>Enter</b> untuk menambah baris peraturan baru secara otomatis.</small>
                    </div>                    

                    <div class="mb-4">
                        <label class="form-label">Foto Unit (Maks 2MB per foto)</label>
                        <div class="upload-box" onclick="document.getElementById('fileInput').click();" id="drop-area">
                            <i class="bi bi-images fs-2 text-primary"></i>
                            <p class="mb-0 mt-2">Klik atau Seret Foto ke Sini</p>
                            <input type="file" id="fileInput" name="foto_kos[]" class="d-none" multiple accept="image/*" onchange="previewImages()">
                        </div>
                        <div id="preview-container" class="row g-3 mt-3"></div>
                    </div>

                    <div class="d-grid mt-5">
                            <button type="submit" id="btnSubmit" class="btn btn-primary py-3 fw-bold">Simpan Data Kos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSukses" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mt-3">Berhasil Tersimpan!</h4>
                <p class="text-muted">Data kos Anda sudah masuk ke sistem kami.</p>
                <a href="daftar_kos.php" class="btn btn-primary px-5">Lihat Daftar Kos</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    
<script>
let selectedFiles = []; // Array untuk menyimpan file yang valid

function previewImages() {
    const input = document.getElementById('fileInput');
    const container = document.getElementById('preview-container');
    const files = Array.from(input.files);
    const maxSize = 2 * 1024 * 1024; // 2MB

    files.forEach((file, index) => {
        // Validasi Ukuran
        if (file.size > maxSize) {
            alert(`File ${file.name} terlalu besar! (Maks 2MB)`);
            return;
        }

        // Simpan ke array global
        selectedFiles.push(file);

        // Buat Elemen Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-auto preview-card';
            div.setAttribute('data-index', selectedFiles.length - 1);
            
            div.innerHTML = `
                <img src="${e.target.result}">
                <button type="button" class="btn-delete-preview" onclick="removeImage(${selectedFiles.length - 1})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });

    // Reset input agar bisa pilih file yang sama jika dihapus
    input.value = ""; 
}

function removeImage(index) {
    // Hapus dari array global
    selectedFiles.splice(index, 1);
    
    // Gambar ulang semua preview agar index-nya sinkron
    renderPreview();
}

function renderPreview() {
    const container = document.getElementById('preview-container');
    container.innerHTML = "";
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-auto preview-card';
            div.innerHTML = `
                <img src="${e.target.result}">
                <button type="button" class="btn-delete-preview" onclick="removeImage(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

document.querySelector('form').onsubmit = function(e) {
    e.preventDefault(); // Mencegah reload halaman
    
    // 1. Siapkan Data (termasuk file gambar)
    const formData = new FormData(this);
    
    // Masukkan file dari array selectedFiles ke dalam FormData
    formData.delete('foto_kos[]'); // Bersihkan input file asli
    selectedFiles.forEach(file => {
        formData.append('foto_kos[]', file);
    });

    // 2. Tampilkan loading pada tombol (opsional tapi bagus)
    const btnSubmit = this.querySelector('button[type="submit"]');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
    btnSubmit.disabled = true;

    // 3. Kirim data via Fetch API (AJAX)
    fetch('proses_tambah_kos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Asumsi jika berhasil, kita munculkan modal
        const myModal = new bootstrap.Modal(document.getElementById('modalSukses'));
        myModal.show();
    })
    .catch(error => {
        alert("Terjadi kesalahan: " + error);
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
    });
};


                    // Logika Peraturan Otomatis
function initList(el) { if (el.value.trim() === "") el.value = "1. "; }
document.getElementById('peraturan_kos').addEventListener('keydown', function(e) {
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
            const insertText = "\n" + nextNum + ". ";
            this.value = textBeforeCursor + insertText + value.substring(pos);
            this.setSelectionRange(pos + insertText.length, pos + insertText.length);
        }
    }
});

                    // Logika area sekitar kos Otomatis
function initList(el) { if (el.value.trim() === "") el.value = "1. "; }
document.getElementById('area_sekitar_kos').addEventListener('keydown', function(e) {
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
            const insertText = "\n" + nextNum + ". ";
            this.value = textBeforeCursor + insertText + value.substring(pos);
            this.setSelectionRange(pos + insertText.length, pos + insertText.length);
        }
    }
});
</script>

</body>
</html>