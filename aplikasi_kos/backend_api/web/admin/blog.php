<?php
require_once '../../config/database.php';
session_start();

$tahun_ini = date('Y');

try {
    $query = "SELECT * FROM blog ORDER BY tgl_dibuat DESC";
    $stmt = $conn->query($query);
    $data_blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_blog = count($data_blog);
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

function tgl_indo($tanggal){
    $bulan = array (1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .blog-card { border: none; border-radius: 20px; background: #fff; transition: all 0.3s ease; }
        .blog-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        /* Ukuran Foto Thumbnail */
        .card-img-left { width: 250px; height: 165px; object-fit: cover; border-radius: 18px; }
        
        /* ATUR JARAK (GAP) DISINI */
        .badge-category { margin-bottom: 12px; display: inline-block; }
        .blog-title { margin-bottom: 10px; font-size: 1.4rem; line-height: 1.3; }
        .blog-meta { margin-bottom: 15px; display: flex; gap: 15px; align-items: center; }
        .blog-excerpt { line-height: 1.6; color: #777; margin-bottom: 0; }

        /* Style Tombol Aksi */
        .btn-action { 
            width: 45px; height: 45px; display: flex; 
            align-items: center; justify-content: center; 
            border-radius: 12px; border: none; transition: 0.2s;
        }
        .btn-action i { font-size: 1.2rem; }
        .btn-action:hover { transform: translateY(-3px); }

        .btn-tambah { background-color: #e1efff; color: #0d6efd; border: none; font-weight: 500; border-radius: 12px; }
        .table-footer { background-color: #fcfcfc; border-top: 1px solid #eee; border-radius: 0 0 20px 20px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-4 py-3">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-1">Jumlah Statistik Blog</h3>
                <p class="text-muted mb-0">Visualisasi data postingan berita tahun <?= $tahun_ini ?>.</p>
            </div>
            <div class="text-end">
                <span class="text-muted small">Total Berita Terbit</span>
                <h1 class="fw-bold text-primary mb-0"><?= number_format($total_blog) ?></h1>
            </div>
        </div>

        <hr class="mb-4">

        <div class="mb-4">
            <a href="tambah_blog.php" class="btn btn-tambah px-4 py-3 shadow-sm">
                <i class="bi bi-plus-circle-fill me-2"></i>Tambah Blog Baru
            </a>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">Daftar Semua Berita</h5>
                    <div class="input-group" style="max-width: 350px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="cariBerita" class="form-control border-start-0 ps-1" placeholder="Cari judul berita...">
                    </div>
                </div>

                <div id="blogContainer">
                    <?php if(empty($data_blog)): ?>
                        <div class="text-center py-5 text-muted">Belum ada berita yang dipublikasikan.</div>
                    <?php else: foreach ($data_blog as $row): ?>
                    <div class="card blog-card border p-3 mb-4 blog-item">
                        <div class="d-flex align-items-center">
                            <?php 
                                $foto = !empty($row['foto_thumbnail']) ? "../../uploads/blog/".$row['foto_thumbnail'] : "https://via.placeholder.com/250x160"; 
                            ?>
                            <img src="<?= $foto ?>" class="card-img-left me-4 shadow-sm">
                            
                            <div class="flex-grow-1">
                                <div class="badge-category">
                                    <span class="badge bg-light text-warning border px-3 py-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">
                                        <?= htmlspecialchars($row['kategori']) ?>
                                    </span>
                                </div>
                                
                                <h4 class="fw-bold blog-title"><?= htmlspecialchars($row['judul']) ?></h4>
                                
                                <div class="blog-meta text-muted small">
                                    <span><i class="bi bi-calendar3 me-2 text-primary"></i><?= tgl_indo($row['tgl_dibuat']) ?></span>
                                    <span><i class="bi bi-person me-2 text-primary"></i>Admin MyKos</span>
                                </div>
                                
                                <p class="blog-excerpt text-truncate" style="max-width: 700px;">
                                    <?= strip_tags($row['isi_konten']) ?>
                                </p>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="edit_blog.php?id=<?= $row['id_blog'] ?>" class="btn-action shadow-sm" title="Edit">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn-action shadow-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalHapus<?= $row['id_blog'] ?>" 
                                        title="Hapus">
                                    <i class="bi bi-trash3-fill text-danger"></i>
                                </button>
                            </div>
                        </div>

                        <div class="modal fade" id="modalHapus<?= $row['id_blog'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                                    <div class="modal-body p-5">
                                        <div class="text-center">
                                            <div class="mb-4">
                                                <div class="bg-light-danger mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 20px; background-color: #fff5f5;">
                                                    <i class="bi bi-trash3 text-danger" style="font-size: 2.5rem;"></i>
                                                </div>
                                            </div>
                                            <h4 class="fw-bold text-dark">Hapus Berita?</h4>
                                            <p class="text-muted px-4">Tindakan ini akan menghapus permanen berita <br><strong>"<?= htmlspecialchars($row['judul']) ?>"</strong>.</p>
                                            
                                            <div class="row g-2 mt-4">
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-light w-100 py-3 fw-semibold text-muted" data-bs-dismiss="modal" style="border-radius: 14px;">Batal</button>
                                                </div>
                                                <div class="col-6">
                                                    <a href="hapus_blog.php?id=<?= $row['id_blog'] ?>" class="btn btn-danger w-100 py-3 fw-semibold" style="border-radius: 14px;">Ya, Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>

                <div class="table-footer p-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-2">Tampilkan</span>
                        <select class="form-select form-select-sm border-0 bg-light" id="limitEntries" style="width: auto; border-radius: 8px;">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-muted small ms-2">Data</span>
                    </div>
                    <div class="text-muted small font-monospace">
                        Visible: <span id="countVisible" class="fw-bold text-primary">0</span> / <?= $total_blog ?> Berita
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 25px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="fw-bold">Data Disimpan!</h3>
                <p class="text-muted">Berita abang sudah berhasil diperbarui dan dipublikasikan.</p>
                <button type="button" class="btn btn-success w-100 py-3 mt-3 shadow-sm" data-bs-dismiss="modal" style="border-radius: 15px; font-weight: 600;">Oke, Siap!</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const items = document.getElementsByClassName('blog-item');
    const limitSelect = document.getElementById('limitEntries');
    const searchInput = document.getElementById('cariBerita');
    const countDisplay = document.getElementById('countVisible');

    // Fungsi Utama Filter & View
    function updateView() {
        let filter = searchInput.value.toUpperCase();
        let limit = limitSelect.value;
        let visibleCount = 0;
        let max = (limit === 'all') ? items.length : parseInt(limit);

        for (let i = 0; i < items.length; i++) {
            let title = items[i].getElementsByTagName('h4')[0].innerText.toUpperCase();
            let match = title.includes(filter);

            if (match && visibleCount < max) {
                items[i].style.display = "";
                visibleCount++;
            } else {
                items[i].style.display = "none";
            }
        }
        countDisplay.innerText = visibleCount;
    }

    // Gabungkan semua logika dalam satu event listener
    document.addEventListener("DOMContentLoaded", function() {
        // Cek URL Status untuk Modal Berhasil
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'updated' || urlParams.get('status') === 'success') {
            var successModal = new bootstrap.Modal(document.getElementById('modalSuccess'));
            successModal.show();
            // Bersihkan URL
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: cleanUrl}, '', cleanUrl);
        }

        // Jalankan View Pertama Kali
        updateView();

        // Event Listeners
        limitSelect.addEventListener('change', updateView);
        searchInput.addEventListener('keyup', updateView);
    });
</script>

</body>
</html>