<?php
require_once '../../config/auth_check.php'; 
require_once '../../config/database.php'; 

$id_user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kos = $_POST['nama_kos'];
    $deskripsi = $_POST['deskripsi'];
    $kota = $_POST['kota'];
    $alamat_lengkap = $_POST['alamat_lengkap']; // SINKRON: Nama variabel disesuaikan
    $nama_pemilik = $_POST['nama_pemilik'];
    $usia = $_POST['usia'];
    $kontak = $_POST['kontak'];

    $target_dir = "../../uploads/profiles/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    function uploadFile($file, $target_dir) {
        if (!isset($file) || $file['error'] !== 0) return false;
        $fileName = time() . '_' . basename($file["name"]);
        $targetFile = $target_dir . $fileName;
        $fileSize = $file["size"];
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($fileSize > 2097152) return false;
        if (!in_array($fileType, ['jpg', 'png', 'jpeg'])) return false;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $fileName;
        }
        return false;
    }

    $foto_ktp = uploadFile($_FILES['foto_ktp'], $target_dir);

    $foto_galeri = [];
    if (isset($_FILES['foto_kos'])) {
        foreach ($_FILES['foto_kos']['name'] as $key => $val) {
            if ($_FILES['foto_kos']['error'][$key] == 0) {
                $file_array = [
                    'name' => $_FILES['foto_kos']['name'][$key],
                    'type' => $_FILES['foto_kos']['type'][$key],
                    'tmp_name' => $_FILES['foto_kos']['tmp_name'][$key],
                    'error' => $_FILES['foto_kos']['error'][$key],
                    'size' => $_FILES['foto_kos']['size'][$key]
                ];
                $upload_name = uploadFile($file_array, $target_dir);
                if ($upload_name) $foto_galeri[] = $upload_name;
            }
        }
    }
    $all_photos = implode(',', $foto_galeri);

    if ($foto_ktp && count($foto_galeri) > 0) {
        try {
            // SINKRON: Menggunakan alamat_lengkap dan foto_ktp sesuai database
            $sql = "INSERT INTO profil_kos (id_user, nama_kos, deskripsi, kota, alamat_lengkap, foto_kos, foto_ktp, nama_pemilik, usia, kontak) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_user, $nama_kos, $deskripsi, $kota, $alamat_lengkap, $all_photos, $foto_ktp, $nama_pemilik, $usia, $kontak]);

            $updateUser = $conn->prepare("UPDATE users SET nama_lengkap = ? WHERE id_user = ?");
            $updateUser->execute([$nama_pemilik, $id_user]);

            $success = true;
        } catch (PDOException $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    } else {
        $error = "Gagal upload. Pastikan Foto Kos dan KTP sudah dipilih (Max 2MB, JPG/PNG).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lengkapi Profil Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .preview-item { position: relative; width: 100px; height: 100px; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; border: 2px solid #dee2e6; }
        .btn-remove { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        #previewKtp img { width: 150px; height: auto; border-radius: 10px; margin-top: 10px; border: 2px solid #dee2e6; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 form-card p-5">
            <h3 class="fw-bold mb-4 text-primary text-center">Lengkapi Profil Kos</h3>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger small"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <h6 class="text-muted fw-bold mb-3"><i class="bi bi-house-door me-2"></i>PROFIL KOS</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Kos</label>
                    <input type="text" name="nama_kos" class="form-control" placeholder="Contoh: Kos SUTOKO" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan keunggulan kos Anda..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary fw-bold">Foto-foto Kos (Bisa banyak)</label>
                        <input type="file" name="foto_kos[]" id="inputFoto" class="form-control" multiple required>
                        <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
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

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold mt-3 shadow">Kirim Data Validasi</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalValidasi" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 p-4 shadow text-center" style="border-radius: 20px;">
            <div class="text-success mb-3"><i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i></div>
            <h5 class="fw-bold">Data Anda Terkirim!</h5>
            <p class="text-muted">Terima kasih <b><?= $_POST['nama_pemilik'] ?? '' ?></b>. Admin akan memvalidasi data Anda. Silakan login kembali dalam 24jam kedepan untuk mengecek status anda.</p>
            
            <a href="../login.php" class="btn btn-primary rounded-3 w-100 py-2">Selesai & Ke Login</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($success)) echo "<script>new bootstrap.Modal(document.getElementById('modalValidasi')).show();</script>"; ?>

<script>
    const MAX_SIZE = 2 * 1024 * 1024; 
    const inputFoto = document.getElementById('inputFoto');
    const previewContainer = document.getElementById('previewContainer');
    let selectedFiles = [];

    inputFoto.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (file.size > MAX_SIZE) {
                alert(`File "${file.name}" terlalu besar! Maksimal 2MB.`);
            } else {
                selectedFiles.push(file);
            }
        });
        renderPreview();
    });

    function renderPreview() {
        previewContainer.innerHTML = ''; 
        selectedFiles.forEach((file, index) => {
            const imgUrl = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${imgUrl}" onload="URL.revokeObjectURL('${imgUrl}')">
                <button type="button" class="btn-remove" onclick="removeFile(${index})">×</button>
            `;
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

    const inputKtp = document.getElementById('inputKtp');
    const previewKtp = document.getElementById('previewKtp');

    inputKtp.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > MAX_SIZE) {
                alert("Foto KTP terlalu besar! Maksimal 2MB.");
                this.value = "";
                previewKtp.innerHTML = "";
                return;
            }
            const imgUrlKtp = URL.createObjectURL(file);
            previewKtp.innerHTML = `<img src="${imgUrlKtp}" class="img-fluid border" style="width:150px; border-radius:10px; margin-top:10px;" onload="URL.revokeObjectURL('${imgUrlKtp}')">`;
        }
    });
</script>
</body>
</html>