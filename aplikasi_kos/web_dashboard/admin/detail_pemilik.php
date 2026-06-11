<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$id_target = $_GET['id'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemilik - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> 
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid" id="contentArea" style="display:none;">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="manajemen_akun.php" class="btn btn-light rounded-circle me-3 shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Detail Profil Pemilik</h3>
                <p class="text-muted mb-0">Informasi lengkap akun dan profil bisnis MyKos.</p>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Profil -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center">
                    <div id="profilFoto" class="mb-3"></div>
                    <h4 id="displayNama" class="fw-bold mb-1"></h4>
                    <span id="displayRole" class="badge bg-light text-primary px-3 py-2 mb-4"></span>
                    
                    <div class="text-start border-top pt-3">
                        <div class="mb-3">
                            <label class="text-muted small d-block">Email Akun</label>
                            <span id="displayEmail" class="fw-medium"></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Kontak</label>
                            <span id="displayKontak" class="fw-medium text-success"></span>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Status Akun</label>
                            <div id="displayStatus"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Bisnis -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-4">Informasi Bisnis & Profil</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Nama Bisnis / Kos</label>
                            <p id="displayNamaKos" class="fw-bold fs-5 text-dark"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Alamat Lengkap Pemilik Kos</label>
                            <p id="displayAlamat" class="fw-semibold"></p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block mb-1">Deskripsi Profil Pemilik & Kos</label>
                            <div class="p-3 bg-light rounded-3">
                                <p id="displayDeskripsi" class="mb-0 text-secondary"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dokumen -->
                <div class="card border-0 shadow-sm p-4 mt-4">
                    <h5 class="fw-bold mb-4">Dokumen & Lampiran</h5>
                    <div class="row g-4" id="dokumenArea"></div>
                </div>
            </div>
        </div>

        <!-- Tabel Kos -->
        <div class="card border-0 shadow-sm p-4 mt-4">
            <h5 class="fw-bold mb-3">Daftar Unit Kos dikelola</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>No.</th><th>Unit</th><th>Harga</th><th>Status</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody id="tabelKosBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FADE UNTUK FOTO -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0 text-center">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <img src="" id="modalImg" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Kos -->
<div class="modal fade" id="modalHapusKos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Kos?</h5>
                <p class="text-muted small">Anda yakin ingin menghapus kos <strong id="hapusKosNama"></strong>?<br>Tindakan ini tidak dapat dibatalkan.</p>
                
                <form id="formHapusKos" class="mt-4">
                    <input type="hidden" name="id_kos" id="hapusKosId">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger w-100 rounded-3">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const idTarget = "<?= $id_target ?>";

async function loadDetail() {
    try {
        const response = await fetch(`../../backend_api/admin/get_detail_pemilik.php?id=${idTarget}`);
        const result = await response.json();

        if (result.status === 'success') {
            const d = result.data;
            
            // Render Profil
            document.getElementById('displayNama').innerText = (d.nama_pemilik || 'User').toUpperCase();
            // Email - bisa diklik (mailto)
            const emailEl = document.getElementById('displayEmail');
            if (d.email) {
                emailEl.innerHTML = `<a href="mailto:${d.email}" class="text-decoration-none fw-medium text-primary">
                                        <i class="bi bi-envelope-fill me-1"></i> ${d.email}
                                    </a>`;
            } else {
                emailEl.innerText = '-';
            }
            // Kontak WhatsApp - bisa diklik
            const kontakEl = document.getElementById('displayKontak');
            if (d.kontak) {
                // Bersihkan nomor (hapus spasi, tanda hubung, dll)
                let cleanNumber = d.kontak.replace(/[^0-9]/g, '');
                // Jika nomor dimulai dengan 0, ganti dengan 62 (kode Indonesia)
                if (cleanNumber.startsWith('0')) {
                    cleanNumber = '62' + cleanNumber.substring(1);
                }
                kontakEl.innerHTML = `<a href="https://wa.me/${cleanNumber}" target="_blank" class="text-decoration-none text-success fw-medium">
                                        <i class="bi bi-whatsapp me-1"></i> ${d.kontak}
                                    </a>`;
            } else {
                kontakEl.innerText = '-';
            }
            document.getElementById('displayRole').innerText = d.role;
            document.getElementById('displayStatus').innerHTML = `<span class="badge ${d.status === 'aktif' ? 'bg-success' : 'bg-warning'} px-3">${d.status.toUpperCase()}</span>`;
            
            // Foto Profil
            const avatarUrl = d.foto_profil ? `../../uploads/${d.foto_profil}` : `https://ui-avatars.com/api/?name=${d.nama_pemilik || 'User'}&background=random&size=128`;
            document.getElementById('profilFoto').innerHTML = `<img src="${avatarUrl}" class="rounded-circle shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border: 4px solid #f8f9fa;">`;

            // Render Bisnis
            document.getElementById('displayNamaKos').innerText = d.nama_kos || 'Belum ada nama';
            document.getElementById('displayAlamat').innerText = d.alamat_lengkap || 'Alamat belum diisi';
            document.getElementById('displayDeskripsi').innerText = d.deskripsi || 'Tidak ada deskripsi.';

            // Render Dokumen (3 Kolom)
            const docs = [
                { label: 'Foto Unit Kos', file: d.foto_kos, folder: 'profil_kos' },
                { label: 'Foto KTP Pemilik', file: d.foto_ktp, folder: 'ktp', blur: true },
                { label: 'Bukti Bayar Registrasi', file: d.bukti_bayar, folder: 'bukti_bayar' }
            ];
            
            let docHtml = '';
            docs.forEach(doc => {
                const imgPath = doc.file ? `../../uploads/${doc.folder}/${doc.file}` : '';
                docHtml += `
                    <div class="col-md-4">
                        <label class="text-muted small d-block mb-2">${doc.label}</label>
                        <img src="${imgPath}" class="img-fluid rounded-3 shadow-sm w-100 preview-img" 
                             style="height: 180px; object-fit: cover; cursor: pointer; ${doc.blur ? 'filter: blur(4px);' : ''}"
                             onclick="showModal('${imgPath}')"
                             onerror="this.src='https://placehold.co/400x300?text=Tidak+Ada'">
                    </div>`;
            });
            document.getElementById('dokumenArea').innerHTML = docHtml;

            // Render Tabel Kos
            let tableHtml = '';
            if (result.kos.length > 0) {
                result.kos.forEach((k, i) => {
                    tableHtml += `<tr>
                        <td>${i+1}</td>
                        <td><div class="fw-bold">${k.nama_kos}</div><small class="text-muted">${k.kota}</small></td>
                        <td>Rp ${parseInt(k.harga_per_bulan).toLocaleString('id-ID')}</td>
                        <td>${k.jumlah_kamar || 0} Kamar</td>
                        <td class="text-center">
                            <a href="detail_kos.php?id=${k.id_kos}" class="btn btn-outline-primary btn-sm me-1" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button onclick="openHapusKosModal(${k.id_kos}, '${k.nama_kos}')" class="btn btn-outline-danger btn-sm" title="Hapus Kos">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                tableHtml = '<tr><td colspan="5" class="text-center py-4">Belum ada unit kos.</td></tr>';
            }
            document.getElementById('tabelKosBody').innerHTML = tableHtml;

            document.getElementById('contentArea').style.display = 'block';
        } else {
            alert(result.message);
        }
    } catch (e) {
        console.error(e);
    }
}

// Fungsi Modal
function showModal(src) {
    if(!src || src.includes('undefined')) return;
    document.getElementById('modalImg').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Deklarasi modal hapus kos
let modalHapusKos;

// Fungsi membuka modal hapus kos
function openHapusKosModal(idKos, namaKos) {
    document.getElementById('hapusKosId').value = idKos;
    document.getElementById('hapusKosNama').innerText = namaKos;
    modalHapusKos.show();
}

// Handler submit hapus kos
const formHapusKos = document.getElementById('formHapusKos');
if (formHapusKos) {
    formHapusKos.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = formHapusKos.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...';
        
        const idKos = document.getElementById('hapusKosId').value;
        const namaKos = document.getElementById('hapusKosNama').innerText;
        
        try {
            const formData = new FormData();
            formData.append('id_kos', idKos);
            
            const response = await fetch('../../backend_api/admin/hapus_kos.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                // Tutup modal
                modalHapusKos.hide();
                // Reload data
                loadDetail();
            } else {
                alert('Gagal menghapus: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    modalHapusKos = new bootstrap.Modal(document.getElementById('modalHapusKos'));
    loadDetail();
});
</script>
</body>
</html>