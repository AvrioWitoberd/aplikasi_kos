<?php
require_once dirname(__DIR__, 2) . '/config/auth_check.php';
require_once dirname(__DIR__, 2) . '/config/database.php'; 

cekAkses('pemilik');

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: daftar_kos.php");
    exit;
}

$id_kos = $_GET['id'];

/* MENYESUAIKAN KEY SESSION
   Jika masih error "Sesi login tidak ditemukan", 
   ganti 'id_user' di bawah ini dengan nama yang ada di auth_check.php kamu (misal: 'id')
*/
$id_user = $_SESSION['id_user'] ?? $_SESSION['id'] ?? $_SESSION['user_id'] ?? null; 

if (!$id_user) {
    // Baris ini untuk bantu kita debug sementara jika masih gagal
    die("Sesi login tidak ditemukan. Isi SESSION Anda adalah: " . print_r($_SESSION, true));
}

try {
    $query = "SELECT * FROM kos WHERE id_kos = :id_kos AND id_pemilik = :id_pemilik";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_kos' => $id_kos, 
        'id_pemilik' => $id_user
    ]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data tidak ditemukan atau Anda tidak memiliki akses ke unit kos ini.");
    }
} catch (PDOException $e) {
    die("Kesalahan Database: " . $e->getMessage());
}
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
        .form-control::placeholder { color: #b9b9b9 !important; opacity: 1; font-weight: 300; }
        .upload-box:hover { border-color: #0d6efd; background: #f8f9fa; }
        
        /* Style khusus foto yang sudah ada */
        .existing-img { opacity: 0.7; border: 2px solid #0d6efd !important; }
        .badge-old { position: absolute; bottom: 5px; left: 5px; font-size: 10px; background: #0d6efd; color: white; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="col-lg-10">
        <div class="card shadow-sm p-4">
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Edit Unit Kos</h3>
                <p class="text-muted">Perbarui informasi kos Anda jika ada perubahan data atau fasilitas.</p>
            </div>

            <form id="formEditKos" enctype="multipart/form-data">
                
                <input type="hidden" name="id_kos" value="<?= $data['id_kos'] ?>">

                <div class="row">
                    <div class="col-md-8 mb-4">
                        <label class="form-label">Nama Kos</label>
                        <input type="text" name="nama_kos" class="form-control" value="<?= $data['nama_kos'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Sisa Kamar (Stok)</label>
                        <input type="number" name="jumlah_kamar" class="form-control" value="<?= $data['jumlah_kamar'] ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Tipe Kos</label>
                        <select name="tipe_kos" class="form-select" required>
                            <option value="putra" <?= $data['tipe_kos'] == 'putra' ? 'selected' : '' ?>>Putra</option>
                            <option value="putri" <?= $data['tipe_kos'] == 'putri' ? 'selected' : '' ?>>Putri</option>
                            <option value="campur" <?= $data['tipe_kos'] == 'campur' ? 'selected' : '' ?>>Campur</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" value="<?= $data['kota'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Harga Per Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_per_bulan" class="form-control" value="<?= $data['harga_per_bulan'] ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi Kos</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi'] ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-control" rows="2" required><?= $data['alamat_lengkap'] ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Fasilitas Kos</label>
                    <input type="text" name="fasilitas_utama" class="form-control" value="<?= $data['fasilitas_utama'] ?>">
                    <small class="text-muted">(Gunakan koma sebagai pemisah)</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Nomor WhatsApp Kos</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="no_hp_kos" class="form-control" value="<?= $data['no_hp_kos'] ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Link Google Maps (Lokasi Kos)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-danger"><i class="bi bi-geo-alt-fill"></i></span>
                        <input type="url" name="link_maps" class="form-control" value="<?= $data['link_maps'] ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Peraturan Kos</label>
                    <textarea id="peraturan_kos" name="peraturan_kos" class="form-control" rows="5" 
                            placeholder="1. Jam malam 22.00&#10;2. Dilarang membawa peliharaan..."
                            onfocus="initList(this)"><?= htmlspecialchars($data['peraturan_kos'] ?? '') ?></textarea>
                    <small class="text-muted">Tekan <b>Enter</b> untuk menambah baris peraturan secara otomatis.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Area Sekitar Kos</label>
                    <textarea id="area_sekitar_kos" name="area_sekitar_kos" class="form-control" rows="5" 
                            placeholder="1. Dilarang mabok di kamar&#10;2. Dilarang membawa pacar dikamar..."
                            onfocus="initList(this)"><?= htmlspecialchars($data['area_sekitar_kos'] ?? '') ?></textarea>
                    <small class="text-muted">Tekan <b>Enter</b> untuk menambah baris area sekitar kos secara otomatis.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Foto Unit</label>
                    
                    <div class="row g-3 mb-3">
                        <p class="small text-muted mb-1">Foto saat ini:</p>
                        <?php
                        $stmt_view = $conn->prepare("SELECT * FROM foto_kos WHERE id_kos = ?");
                        $stmt_view->execute([$id_kos]);
                        $fotos = $stmt_view->fetchAll(PDO::FETCH_ASSOC);

                        if ($fotos) {
                            foreach($fotos as $f) : 
                                // GUNAKAN 'file_nama' sesuai gambar struktur DB Anda
                                $nama_file = $f['file_nama'] ?? '';
                                $id_foto = $f['id_foto'];
                                
                                if ($nama_file !== '') :
                                    $path_tampil = "../../uploads/kos/" . $nama_file;
                        ?>
                                <div class="col-auto preview-card" id="foto-container-<?= $id_foto ?>" style="width: 120px; height: 120px; position: relative;">
                                    <img src="<?= $path_tampil ?>" class="existing-img" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    
                                    <button type="button" 
                                            onclick="hapusFotoLama(<?= $id_foto ?>, '<?= $nama_file ?>')"
                                            style="position: absolute; top: -5px; right: -5px; background: #ff0000; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <span class="badge bg-primary" style="position: absolute; bottom: 5px; left: 5px; font-size: 10px;">Lama</span>
                                </div>
                        <?php 
                                endif;
                            endforeach; 
                        } else {
                            echo '<p class="text-muted small ms-2">Data foto tidak ditemukan.</p>';
                        }
                        ?>
                    </div>

                    <div class="upload-box" onclick="document.getElementById('fileInput').click();">
                        <i class="bi bi-images fs-2 text-primary"></i>
                        <p class="mb-0 mt-2">Klik untuk Tambah/Ganti Foto Baru</p>
                        <input type="file" id="fileInput" class="d-none" multiple accept="image/*" onchange="previewImages()">
                    </div>
                    <div id="preview-container" class="row g-3 mt-3"></div>
                    <small class="text-info">* Mengunggah foto baru akan mengganti foto lama atau menambah (tergantung logika proses_edit Anda).</small>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" id="btnSubmit" class="btn btn-warning py-3 fw-bold">Update Data Kos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSukses" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                </div>
                <h5>Data Berhasil Diperbarui!</h5>
                <p class="text-muted small">Semua perubahan telah disimpan ke sistem.</p>
                <button type="button" class="btn btn-primary w-100" onclick="window.location.href='daftar_kos.php'">Selesai</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let selectedFiles = [];

// FUNGSI 1: Untuk menghapus foto LAMA (yang sudah ada di database)
function hapusFotoLama(idFoto, namaFile) {
    // BARIS KONFIRMASI DIHAPUS AGAR LANGSUNG PROSES
    const elemen = document.getElementById('foto-container-' + idFoto);
    if (elemen) elemen.style.opacity = '0.3'; // Efek visual saat proses hapus

    const formData = new FormData();
    formData.append('aksi', 'hapus_foto_lama');
    formData.append('id_foto', idFoto);
    formData.append('nama_file', namaFile);

    fetch('proses_edit_kos.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success_hapus") {
            elemen.remove(); // Foto langsung hilang dari tampilan
        } else {
            elemen.style.opacity = '1';
            console.error("Gagal menghapus: " + data);
        }
    })
    .catch(err => {
        elemen.style.opacity = '1';
        console.error(err);
    });
}

// FUNGSI 2: Preview foto baru yang dipilih
function previewImages() {
    const input = document.getElementById('fileInput');
    const files = Array.from(input.files);
    files.forEach(file => {
        if (file.size <= 2 * 1024 * 1024) {
            selectedFiles.push(file);
        } else {
            alert("File " + file.name + " terlalu besar (maks 2MB)");
        }
    });
    input.value = ""; 
    renderPreview();
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
            div.className = 'col-auto preview-card';
            div.innerHTML = `<img src="${e.target.result}"><button type="button" class="btn-delete-preview" onclick="removeImage(${index})"><i class="bi bi-x"></i></button>`;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

// FUNGSI 3: Simpan Data (Hanya satu handler saja)
document.getElementById('formEditKos').onsubmit = function(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btnSubmit');
    const originalText = btnSubmit.innerHTML;
    
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
    btnSubmit.disabled = true;

    const formData = new FormData(this);
    
    // Masukkan file baru ke formData
    selectedFiles.forEach(file => { 
        formData.append('foto_kos[]', file); 
    });

    fetch('proses_edit_kos.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        console.log("Response:", data);
        if (data.trim() === "success") {
            const myModal = new bootstrap.Modal(document.getElementById('modalSukses'));
            myModal.show();
        } else {
            alert("Gagal menyimpan: " + data);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    })
    .catch(err => {
        alert("Terjadi kesalahan sistem.");
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;
    });
};

                    // Logika Peraturan & area sekitar Otomatis
function initList(el) { 
    if (el.value.trim() === "") el.value = "1. "; 
}

// Terapkan ke semua textarea dengan ID yang mengandung 'peraturan' atau 'area'
['peraturan_kos', 'area_sekitar_kos'].forEach(id => {
    const textarea = document.getElementById(id);
    if (textarea) {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const pos = this.selectionStart;
                const value = this.value;
                const textBeforeCursor = value.substring(0, pos);
                const lines = textBeforeCursor.split('\n');
                const currentLine = lines[lines.length - 1];
                const match = currentLine.match(/^(\d+)\.\s/);

                if (match) {
                    const nextNum = parseInt(match[1]) + 1;
                    const insertText = "\n" + nextNum + ". ";
                    this.value = textBeforeCursor + insertText + value.substring(pos);
                    this.setSelectionRange(pos + insertText.length, pos + insertText.length);
                } else {
                    // Cari nomor terakhir dari semua baris
                    let maxNum = 0;
                    const allLines = this.value.split('\n');
                    for (let line of allLines) {
                        const lineMatch = line.match(/^(\d+)\.\s/);
                        if (lineMatch) {
                            maxNum = Math.max(maxNum, parseInt(lineMatch[1]));
                        }
                    }
                    const nextNum = maxNum + 1;
                    const insertText = "\n" + nextNum + ". ";
                    this.value = textBeforeCursor + insertText + value.substring(pos);
                    this.setSelectionRange(pos + insertText.length, pos + insertText.length);
                }
            }
        });
    }
});
</script>

</body>
</html>