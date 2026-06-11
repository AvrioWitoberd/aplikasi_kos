<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
// Tidak perlu require database di sini karena kita pakai API
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemilik - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Kelola Pemilik Kos</h2>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari pemilik...">
                    </div>
                    <select id="filterSelect" class="form-select" style="width: 150px;">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                    </select>
                    <select id="filterStatus" class="form-select" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="pending">Pending</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Tempat Data Akan Muncul -->
            <div id="ownerListContainer" class="list-container mt-64">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data pemilik...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- modal edit status -->
<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Ubah Status Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUpdateStatus">
                <div class="modal-body pt-3">
                    <p class="text-muted small mb-3">Pilih status untuk: <strong id="modalUserName"></strong></p>
                    <input type="hidden" name="id_user" id="modalIdUser">
                    
                    <input type="radio" name="status" value="aktif" id="statusAktif" class="form-check-input d-none">
                    <label for="statusAktif" class="w-100 mb-2" style="cursor:pointer;">
                        <div class="status-option p-3 border rounded-3 border-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> Aktif
                        </div>
                    </label>

                    <input type="radio" name="status" value="nonaktif" id="statusNonAktif" class="form-check-input d-none">
                    <label for="statusNonAktif" class="w-100" style="cursor:pointer;">
                        <div class="status-option p-3 border rounded-3 border-2">
                            <i class="bi bi-x-circle-fill text-danger me-2"></i> Non-Aktif
                        </div>
                    </label>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Akun?</h5>
                <p class="text-muted small">Anda yakin ingin menghapus akun <strong id="hapusUserName"></strong>?</p>
                
                <form id="formHapusUser" class="mt-4">
                    <input type="hidden" name="id_user" id="hapusIdUser">
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
// Deklarasi variable modal global
let modalStatus, modalHapus;
let originalUsersData = [];

// Fungsi untuk mengambil data dari API
async function fetchPemilik() {
    try {
        const response = await fetch('../../backend_api/admin/get_pemilik.php');
        const result = await response.json();

        if (result.status === 'success') {
            originalUsersData = result.data;
            renderData(originalUsersData);
        } else {
            document.getElementById('ownerListContainer').innerHTML = `<div class="alert alert-danger">Gagal mengambil data: ${result.message}</div>`;
        }
    } catch (error) {
        console.error("Error:", error);
        document.getElementById('ownerListContainer').innerHTML = `<div class="alert alert-danger">Terjadi kesalahan koneksi ke API.</div>`;
    }
}

// Fungsi untuk menampilkan data ke HTML
function renderData(users) {
    const container = document.getElementById('ownerListContainer');
    container.innerHTML = '';

    if (users.length === 0) {
        container.innerHTML = '<div class="text-center p-5">Belum ada data pemilik.</div>';
        return;
    }

    users.forEach((user, index) => {
        const inisial = user.nama_lengkap
            ? user.nama_lengkap.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
            : '??';
        
        const statusClass = user.status === 'aktif' ? 'status-aktif' : 'status-pending';
        
        // Escape string untuk menghindari error karakter khusus
        const safeNama = user.nama_lengkap.replace(/'/g, "\\'");
        const safeEmail = user.email.replace(/'/g, "\\'");
        
        const card = `
            <div class="owner-card shadow-sm">
                <div class="me-4 text-muted fw-bold" style="width: 20px;">${index + 1}</div>
                <div class="avatar-circle me-4">${inisial}</div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">${user.nama_lengkap}</h6>
                </div>
                <div class="flex-grow-1 text-muted">
                    ${user.email}
                </div>
                <div class="me-4">
                    <span class="badge-status ${statusClass}">
                        <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> 
                        ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                    </span>
                </div>
                <div class="d-flex">
                    <a href="detail_pemilik.php?id=${user.id_user}" class="action-btn btn-view" title="Lihat Profil">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       onclick="openEditModal('${user.id_user}', '${safeNama}', '${user.status}', '${user.email}')"
                       class="action-btn btn-edit" 
                       title="Edit Akun">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       onclick="openHapusModal('${user.id_user}', '${safeNama}')" 
                       class="action-btn btn-delete" 
                       title="Hapus Akun">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        `;
        container.innerHTML += card;
    });
}

/* ==========================================
   FUNGSI UNTUK MODAL STATUS
   ========================================== */

function openEditModal(id, nama, status, email = '') {
    document.getElementById('modalIdUser').value = id;
    document.getElementById('modalUserName').innerText = nama;
    
    // Tambahan: tampilkan email jika ada
    const emailElement = document.getElementById('modalUserEmail');
    if (emailElement) {
        emailElement.innerText = email;
    }
    
    // LOGIKA YANG BENAR: Jika status saat ini 'aktif', centang 'aktif'
    if (status === 'aktif') {
        document.getElementById('statusAktif').checked = true;
        document.getElementById('statusNonAktif').checked = false;
    } else {
        document.getElementById('statusAktif').checked = false;
        document.getElementById('statusNonAktif').checked = true;
    }
    
    modalStatus.show();
}

/* ==========================================
   FUNGSI UNTUK MODAL HAPUS
   ========================================== */

function openHapusModal(id, nama) {
    document.getElementById('hapusIdUser').value = id;
    document.getElementById('hapusUserName').innerText = nama;
    modalHapus.show();
}

// Inisialisasi modal dan event listener saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi modal
    modalStatus = new bootstrap.Modal(document.getElementById('modalStatus'));
    modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
    
    // Load data
    fetchPemilik();
});

// Handler Submit Update Status (Tanpa alert & reload)
const formUpdateStatus = document.getElementById('formUpdateStatus');
if (formUpdateStatus) {
    formUpdateStatus.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const submitBtn = formUpdateStatus.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        try {
            const response = await fetch('../../backend_api/admin/update_status.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalStatus'));
                modal.hide();
                
                // Update status di tampilan tanpa reload
                const idUser = formData.get('id_user');
                const newStatus = formData.get('status');
                updateStatusInUI(idUser, newStatus);
            } else {
                // Tampilkan error di dalam modal (bukan alert)
                showModalError('modalStatus', result.message);
            }
        } catch (error) {
            console.error("Error:", error);
            showModalError('modalStatus', 'Terjadi kesalahan sistem');
        } finally {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Handler Submit Hapus User (Tanpa alert & reload)
const formHapusUser = document.getElementById('formHapusUser');
if (formHapusUser) {
    formHapusUser.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const submitBtn = formHapusUser.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...';

        try {
            const response = await fetch('../../backend_api/admin/hapus_user.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalHapus'));
                modal.hide();
                
                // Hapus card dari UI tanpa reload
                const idUser = formData.get('id_user');
                removeUserFromUI(idUser);
            } else {
                showModalError('modalHapus', result.message);
            }
        } catch (error) {
            console.error("Error:", error);
            showModalError('modalHapus', 'Terjadi kesalahan sistem');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Fungsi untuk update status di UI tanpa reload
function updateStatusInUI(idUser, newStatus) {
    // Cari card yang berisi user dengan id tersebut
    const cards = document.querySelectorAll('.owner-card');
    for (let card of cards) {
        const link = card.querySelector(`a[onclick*="openEditModal('${idUser}'"]`);
        if (link || card.innerHTML.includes(`id=${idUser}`)) {
            // Update badge status
            const badgeSpan = card.querySelector('.badge-status');
            if (badgeSpan) {
                const statusClass = newStatus === 'aktif' ? 'status-aktif' : 'status-pending';
                const statusText = newStatus === 'aktif' ? 'Aktif' : 'Nonaktif';
                
                badgeSpan.className = `badge-status ${statusClass}`;
                badgeSpan.innerHTML = `<i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> ${statusText}`;
            }
            break;
        }
    }
}

// Fungsi untuk menghapus card dari UI tanpa reload
function removeUserFromUI(idUser) {
    const cards = document.querySelectorAll('.owner-card');
    for (let card of cards) {
        const link = card.querySelector(`a[onclick*="openEditModal('${idUser}'"]`);
        if (link || card.innerHTML.includes(`id=${idUser}`)) {
            card.remove();
            break;
        }
    }
    
    // Update ulang nomor urut
    updateRowNumbers();
}

// Fungsi update nomor urut setelah hapus
function updateRowNumbers() {
    const cards = document.querySelectorAll('.owner-card');
    cards.forEach((card, index) => {
        const numberDiv = card.querySelector('.me-4.text-muted.fw-bold');
        if (numberDiv) {
            numberDiv.innerText = index + 1;
        }
    });
}

// Fungsi untuk filter data berdasarkan pilihan (terbaru/terlama & status)
function applyFilterAndSearch() {
    const filterValue = document.getElementById('filterSelect').value;
    const filterStatus = document.getElementById('filterStatus').value;
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    
    let filteredData = [...originalUsersData];
    
    // Filter berdasarkan search
    if (keyword) {
        filteredData = filteredData.filter(user => 
            (user.nama_lengkap?.toLowerCase().includes(keyword) || 
             user.email?.toLowerCase().includes(keyword))
        );
    }
    
    // Filter berdasarkan status
    if (filterStatus) {
        filteredData = filteredData.filter(user => user.status === filterStatus);
    }
    
    // Urutkan berdasarkan created_at
    if (filterValue === 'terbaru') {
        filteredData.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (filterValue === 'terlama') {
        filteredData.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    }
    
    renderData(filteredData);
}

// Fungsi show error di dalam modal (bukan alert)
function showModalError(modalId, message) {
    const modal = document.getElementById(modalId);
    let errorDiv = modal.querySelector('.modal-error-message');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'modal-error-message alert alert-danger alert-dismissible fade show mt-3 mb-0';
        errorDiv.style.borderRadius = '12px';
        errorDiv.style.padding = '10px 15px';
        errorDiv.style.fontSize = '13px';
        
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'alert');
        closeBtn.style.fontSize = '10px';
        errorDiv.appendChild(closeBtn);
        
        const modalBody = modal.querySelector('.modal-body');
        modalBody.appendChild(errorDiv);
    }
    
    errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${message}`;
    errorDiv.style.display = 'block';
    
    // Auto hide setelah 3 detik
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 3000);
}

// Event listener untuk search dan filter
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    applyFilterAndSearch();
});

document.getElementById('filterSelect')?.addEventListener('change', function() {
    applyFilterAndSearch();
});

// Tambahkan event listener untuk filter status
document.getElementById('filterStatus')?.addEventListener('change', function() {
    applyFilterAndSearch();
});
</script>

</body>
</html>