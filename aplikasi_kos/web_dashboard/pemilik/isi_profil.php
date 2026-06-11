<?php
session_start();
// Cek login
if(!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lengkapi Profil Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="stylesheet" href="../assets/css/style_mobile.css?v=1.1">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .preview-item { position: relative; width: 100px; height: 100px; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; border: 2px solid #dee2e6; }
        .btn-remove { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 form-card p-5">
            <h3 class="fw-bold mb-4 text-primary text-center">Lengkapi Infromasi Bisnis & Profil</h3>
            
            <div id="alertBox" class="alert alert-danger small" style="display:none;"></div>

            <form id="formProfil" enctype="multipart/form-data">
                <h6 class="text-muted fw-bold mb-3"><i class="bi bi-house-door me-2"></i>PROFIL KOS</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Profil Kos</label>
                    <input type="text" name="nama_kos" class="form-control" placeholder="Contoh: Kos Griyashanta Suhat" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Profil Pemilik & Kos Anda</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsikan profil anda dan kos anda saat ini..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary fw-bold">Foto-foto Kos</label>
                        <input type="file" name="foto_kos[]" id="inputFoto" class="form-control" multiple required>
                        <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap Anda Saat Ini</label>
                    <textarea name="alamat_lengkap" class="form-control" required></textarea>
                </div>

                <hr class="my-4">
                <h6 class="text-muted fw-bold mb-3"><i class="bi bi-person me-2"></i>PROFIL PEMILIK</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap Pemilik</label>
                    <input type="text" name="nama_pemilik" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Usia</label>
                        <input type="number" name="usia" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Kontak/WA</label>
                        <input type="text" name="kontak" class="form-control" placeholder="08123xxx" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-danger fw-bold">Upload Foto KTP</label>
                    <input type="file" name="foto_ktp" id="inputKtp" class="form-control border-danger" required>
                    <div id="previewKtp"></div>
                </div>

                <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Informasi Pembayaran Pendaftaran</h6>
                            <p class="small mb-0">Silakan lakukan pembayaran sejumlah <b>Rp 500.000</b> ke Rekening <b>BCA 123456789 a/n MyKos Indonesia</b> untuk pendaftaran sebagai pemilik kos.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="bi bi-receipt me-1"></i> Upload Bukti Pembayaran</label>
                    <input type="file" name="bukti_bayar" id="inputBayar" class="form-control" required>
                    <div id="previewBayar"></div>
                </div>
                <hr class="my-4">

                <button type="submit" id="btnKirim" class="btn btn-primary w-100 py-3 rounded-3 fw-bold mt-3 shadow">Kirim Data Validasi</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalValidasi" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 p-4 shadow text-center" style="border-radius: 20px;">
            <div class="text-success mb-3"><i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i></div>
            <h5 class="fw-bold">Data Anda Terkirim!</h5>
            <p class="text-muted">Terima kasih <b id="displayNama"></b>. Admin akan memvalidasi data Anda dalam 24 jam.</p>
            <a href="../logout.php" class="btn btn-primary rounded-3 w-100 py-2">Selesai & Ke Login</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Logic AJAX Submit
    $('#formProfil').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnKirim');
        const formData = new FormData(this);

        btn.prop('disabled', true).text('Sedang Mengirim...');

        $.ajax({
            url: "../../backend_api/profile/submit_profil_pemilik.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if(res.status === 'success') {
                    $('#displayNama').text(res.nama_pemilik);
                    new bootstrap.Modal(document.getElementById('modalValidasi')).show();
                } else {
                    alert("Gagal: " + res.message);
                    btn.prop('disabled', false).text('Kirim Data Validasi');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Terjadi kesalahan sistem: " + xhr.responseText);
                btn.prop('disabled', false).text('Kirim Data Validasi');
            }
        });
    }); // <--- TADI ABANG KURANG PENUTUP INI

    const MAX_SIZE = 2 * 1024 * 1024;
    const inputFoto = document.getElementById('inputFoto');
    const previewContainer = document.getElementById('previewContainer');
    let selectedFiles = [];

    inputFoto.addEventListener('change', function(e) {
        Array.from(e.target.files).forEach(file => {
            if (file.size > MAX_SIZE) alert(`File "${file.name}" > 2MB!`);
            else selectedFiles.push(file);
        });
        renderPreview();
    });

    function renderPreview() {
        previewContainer.innerHTML = ''; 
        selectedFiles.forEach((file, index) => {
            const imgUrl = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `<img src="${imgUrl}"><button type="button" class="btn-remove" onclick="removeFile(${index})">×</button>`;
            previewContainer.appendChild(div);
        });
        updateInputFile();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        renderPreview();
    }

    function updateInputFile() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        inputFoto.files = dataTransfer.files;
    }

    $('#inputKtp').on('change', function() {
        const file = this.files[0];
        if (file && file.size <= MAX_SIZE) {
            const reader = new FileReader();
            reader.onload = (e) => $('#previewKtp').html(`<img src="${e.target.result}" style="width:150px; border-radius:10px; margin-top:10px; border:2px solid #dee2e6;">`);
            reader.readAsDataURL(file);
        }
    });

    $('#inputBayar').on('change', function() {
        const file = this.files[0];
        if (file && file.size <= MAX_SIZE) {
            const reader = new FileReader();
            reader.onload = (e) => $('#previewBayar').html(`<img src="${e.target.result}" style="width:150px; border-radius:10px; margin-top:10px; border:2px solid #dee2e6;">`);
            reader.readAsDataURL(file);
        }
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

</body>
</html>