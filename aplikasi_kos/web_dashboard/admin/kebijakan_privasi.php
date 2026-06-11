<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <style>
        .rich-editor-wrapper {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }
        .rich-editor-toolbar {
            background: #f8f9fa;
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .editor-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .editor-btn:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }
        .rich-editor-content {
            padding: 16px;
            min-height: 160px;
            background: white;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
        }
        .rich-editor-content:focus {
            outline: none;
        }
        .section-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .section-view {
            line-height: 1.6;
        }
        .intro-text {
            background: #e8f4fd;
            border-left: 4px solid #0d6efd;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .intro-text .btn-edit-intro {
            height: 36px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">
                            <i class="bi bi-shield-lock text-primary me-2"></i>
                            Kebijakan Privasi
                        </h2>
                        <p class="text-muted">Aplikasi My Kos (Icarus Developer)</p>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4 py-2" id="btnTambahSection">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Section
                    </button>
                </div>
            </div>

            <!-- Intro Teks -->
            <div id="introContainer" class="mb-4"></div>

            <!-- Form Tambah Section -->
            <div id="formTambahSection" class="card border-0 shadow-sm mb-4" style="border-radius: 20px; display: none;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tambah Section Baru</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Section</label>
                        <input type="text" id="newJudul" class="form-control" placeholder="Contoh: 1. Pengumpulan dan Penggunaan Informasi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Isi Konten</label>
                        <div class="rich-editor-wrapper">
                            <div class="rich-editor-toolbar">
                                <button type="button" class="editor-btn" onclick="formatEditor('newEditor', 'bold')"><b>B</b></button>
                                <button type="button" class="editor-btn" onclick="formatEditor('newEditor', 'italic')"><i>I</i></button>
                                <button type="button" class="editor-btn" onclick="formatEditor('newEditor', 'underline')"><u>U</u></button>
                                <button type="button" class="editor-btn" onclick="formatEditor('newEditor', 'insertUnorderedList')">📋</button>
                                <button type="button" class="editor-btn" onclick="formatEditor('newEditor', 'insertOrderedList')">1.</button>
                            </div>
                            <div id="newEditor" class="rich-editor-content" contenteditable="true"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-light rounded-pill px-4 py-2" id="btnBatalTambah">Batal</button>
                        <button class="btn btn-primary rounded-pill px-4 py-2" id="btnSimpanTambah">Simpan</button>
                    </div>
                </div>
            </div>

            <!-- Container Daftar Section -->
            <div id="policyContainer">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat kebijakan privasi...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Hapus Section -->
<div class="modal fade" id="modalHapusSection" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Section?</h5>
                <p class="text-muted small">Anda yakin ingin menghapus section <strong id="hapusSectionJudul"></strong>?</p>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-light w-100 rounded-3 py-2" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmHapus" class="btn btn-danger w-100 rounded-3 py-2">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Intro -->
<div class="modal fade" id="modalEditIntro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Intro Teks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Intro Teks</label>
                    <div class="rich-editor-wrapper">
                        <div class="rich-editor-toolbar">
                            <button type="button" class="editor-btn" onclick="formatEditor('introEditor', 'bold')"><b>B</b></button>
                            <button type="button" class="editor-btn" onclick="formatEditor('introEditor', 'italic')"><i>I</i></button>
                            <button type="button" class="editor-btn" onclick="formatEditor('introEditor', 'underline')"><u>U</u></button>
                            <button type="button" class="editor-btn" onclick="formatEditor('introEditor', 'insertUnorderedList')">📋</button>
                            <button type="button" class="editor-btn" onclick="formatEditor('introEditor', 'insertOrderedList')">1.</button>
                        </div>
                        <div id="introEditor" class="rich-editor-content" contenteditable="true"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSimpanIntro">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let hapusId = null;
let modalHapus;
let modalIntro;
let introText = '';

function formatEditor(editorId, command) {
    const editor = document.getElementById(editorId);
    if (editor) {
        editor.focus();
        document.execCommand(command, false, null);
    }
}

async function loadPolicy() {
    try {
        const response = await fetch('../../backend_api/kebijakan_privasi/get_kebijakan.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            introText = result.intro_text || '';
            renderIntro(introText);
            renderSections(result.data);
        } else {
            document.getElementById('policyContainer').innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('policyContainer').innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
    }
}

function renderIntro(intro) {
    const container = document.getElementById('introContainer');
    const html = `
        <div class="intro-text">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">${intro || '<p class="text-muted mb-0">Belum ada intro teks. Klik tombol edit untuk menambahkan.</p>'}</div>
                <button class="btn btn-sm btn-outline-primary rounded-pill ms-3" onclick="openEditIntro()" style="height: 36px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </div>
        </div>
    `;
    container.innerHTML = html;
}

function openEditIntro() {
    const editor = document.getElementById('introEditor');
    if (editor) editor.innerHTML = introText;
    modalIntro.show();
}

document.getElementById('btnSimpanIntro').addEventListener('click', async () => {
    const newIntro = document.getElementById('introEditor').innerHTML;
    const formData = new FormData();
    formData.append('intro_text', newIntro);
    
    try {
        const response = await fetch('../../backend_api/kebijakan_privasi/update_intro.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            introText = newIntro;
            renderIntro(introText);
            modalIntro.hide();
            showToast('Intro teks berhasil diperbarui!', 'success');
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    }
});

function renderSections(sections) {
    if (!sections || sections.length === 0) {
        document.getElementById('policyContainer').innerHTML = `
            <div class="text-center p-5">
                <i class="bi bi-shield-slash fs-1 text-muted"></i>
                <p class="mt-2 text-muted">Belum ada kebijakan privasi. Klik "Tambah Section" untuk mulai.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    sections.forEach(section => {
        // Skip intro section (id=1) jika ada
        if (section.id == 1) return;
        
        html += `
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;" data-id="${section.id}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="section-title mb-0">${escapeHtml(section.judul_section || '')}</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-warning btn-sm rounded-pill px-3" onclick="openEditSection(${section.id})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="openHapusModal(${section.id}, '${escapeHtml(section.judul_section)}')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                    <div id="viewContent_${section.id}" class="section-view">${section.isi_konten || '<p class="text-muted">Belum ada konten.</p>'}</div>
                    
                    <!-- Form Edit -->
                    <div id="editForm_${section.id}" class="mt-4" style="display: none;">
                        <div class="border-top pt-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Judul Section</label>
                                <input type="text" id="editJudul_${section.id}" class="form-control" value="${escapeHtml(section.judul_section)}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Isi Konten</label>
                                <div class="rich-editor-wrapper">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="editor-btn" onclick="formatEditor('editEditor_${section.id}', 'bold')"><b>B</b></button>
                                        <button type="button" class="editor-btn" onclick="formatEditor('editEditor_${section.id}', 'italic')"><i>I</i></button>
                                        <button type="button" class="editor-btn" onclick="formatEditor('editEditor_${section.id}', 'underline')"><u>U</u></button>
                                        <button type="button" class="editor-btn" onclick="formatEditor('editEditor_${section.id}', 'insertUnorderedList')">📋</button>
                                        <button type="button" class="editor-btn" onclick="formatEditor('editEditor_${section.id}', 'insertOrderedList')">1.</button>
                                    </div>
                                    <div id="editEditor_${section.id}" class="rich-editor-content" contenteditable="true">${section.isi_konten || ''}</div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-light rounded-pill px-4 py-2" onclick="closeEditSection(${section.id})">Batal</button>
                                <button class="btn btn-primary rounded-pill px-4 py-2" onclick="saveEditSection(${section.id})">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    document.getElementById('policyContainer').innerHTML = html;
}

function openEditSection(id) {

    const viewDiv = document.getElementById(`viewContent_${id}`);
    const editForm = document.getElementById(`editForm_${id}`);

    if (viewDiv) {
        viewDiv.style.display = 'none';
    }

    if (editForm) {
        editForm.style.display = 'block';
    }
}

function closeEditSection(id) {
    const viewDiv = document.getElementById(`viewContent_${id}`);
    const editForm = document.getElementById(`editForm_${id}`);
    
    if (viewDiv) viewDiv.style.display = 'block';
    if (editForm) editForm.style.display = 'none';
}

async function saveEditSection(id) {
    const judul = document.getElementById(`editJudul_${id}`).value;
    const konten = document.getElementById(`editEditor_${id}`).innerHTML;
    
    // Judul boleh kosong, tidak perlu validasi
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('judul_section', judul);
    formData.append('isi_konten', konten);
    
    try {
        const response = await fetch('../../backend_api/kebijakan_privasi/update_section.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            showToast('Section berhasil diperbarui!', 'success');
            loadPolicy();
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    }
}

function openHapusModal(id, judul) {
    hapusId = id;
    document.getElementById('hapusSectionJudul').innerText = judul;
    modalHapus.show();
}

document.getElementById('btnConfirmHapus').addEventListener('click', async () => {
    if (!hapusId) return;
    
    const formData = new FormData();
    formData.append('id', hapusId);
    
    try {
        const response = await fetch('../../backend_api/kebijakan_privasi/hapus_section.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.status === 'success') {
            modalHapus.hide();
            showToast('Section berhasil dihapus!', 'success');
            loadPolicy();
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    }
});

// Form Tambah Section
const btnTambah = document.getElementById('btnTambahSection');
const formTambah = document.getElementById('formTambahSection');
const btnBatalTambah = document.getElementById('btnBatalTambah');
const btnSimpanTambah = document.getElementById('btnSimpanTambah');
const newEditor = document.getElementById('newEditor');

if (btnTambah) {
    btnTambah.onclick = () => {
        formTambah.style.display = 'block';
        btnTambah.style.display = 'none';
        document.getElementById('newJudul').value = '';
        if (newEditor) newEditor.innerHTML = '';
    };
}

if (btnBatalTambah) {
    btnBatalTambah.onclick = () => {
        formTambah.style.display = 'none';
        btnTambah.style.display = 'inline-flex';
        document.getElementById('newJudul').value = '';
        if (newEditor) newEditor.innerHTML = '';
    };
}

if (btnSimpanTambah) {
    btnSimpanTambah.onclick = async () => {
        const judul = document.getElementById('newJudul').value;
        const konten = newEditor ? newEditor.innerHTML : '';
        
        // Judul boleh kosong - tidak perlu validasi
        
        const formData = new FormData();
        formData.append('judul_section', judul);
        formData.append('isi_konten', konten);
        
        try {
            const response = await fetch('../../backend_api/kebijakan_privasi/tambah_section.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                showToast('Section berhasil ditambahkan!', 'success');
                formTambah.style.display = 'none';
                btnTambah.style.display = 'inline-flex';
                document.getElementById('newJudul').value = '';
                if (newEditor) newEditor.innerHTML = '';
                loadPolicy();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', 'error');
        }
    };
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `position-fixed bottom-0 end-0 p-3 m-3 rounded-3 shadow text-white ${type === 'success' ? 'bg-success' : 'bg-danger'}`;
    toast.style.zIndex = '9999';
    toast.style.minWidth = '250px';
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
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

document.addEventListener('DOMContentLoaded', function() {
    modalHapus = new bootstrap.Modal(document.getElementById('modalHapusSection'));
    modalIntro = new bootstrap.Modal(document.getElementById('modalEditIntro'));
    loadPolicy();
});
</script>

</body>
</html>