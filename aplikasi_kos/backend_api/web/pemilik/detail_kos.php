<?php
require_once '../../config/auth_check.php';
require_once '../../config/database.php'; // File ini berisi $conn yang bertipe PDO
cekAkses('pemilik');

// Ambil ID dari URL
$id_kos = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id_kos)) {
    header("Location: daftar_kos.php");
    exit;
}

try {
    // 1. Query Data Kos & Pemilik menggunakan PDO Prepared Statements (Lebih Aman)
    $query = "SELECT k.*, u.nama_lengkap as nama_pemilik 
              FROM kos k 
              JOIN users u ON k.id_pemilik = u.id_user 
              WHERE k.id_kos = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $id_kos]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Data kos tidak ditemukan!";
        exit;
    }

    // Query tambahan untuk mengambil rata-rata rating
    $query_rating = "SELECT AVG(skor_rating) as rata_rating, COUNT(*) as total_ulasan 
                     FROM rating 
                     WHERE id_kos = :id";
    $stmt_rating = $conn->prepare($query_rating);
    $stmt_rating->execute(['id' => $id_kos]);
    $rating_data = $stmt_rating->fetch(PDO::FETCH_ASSOC);

    $rata_rating = number_format($rating_data['rata_rating'] ?? 0, 1);
    $total_ulasan = $rating_data['total_ulasan'] ?? 0;

    // 2. Query Foto-foto Kos
    $query_foto = "SELECT file_nama FROM foto_kos WHERE id_kos = :id";
    $stmt_foto = $conn->prepare($query_foto);
    $stmt_foto->execute(['id' => $id_kos]);
    $fotos = $stmt_foto->fetchAll(PDO::FETCH_COLUMN);

    // 3. Logic Olah Data String ke Array
    $fasilitas = explode(',', $data['fasilitas_utama']);

    // Fungsi untuk mengekstrak koordinat dari link Google Maps
    function getCoordinatesFromMapsLink($link) {
        if (empty($link)) return null;
        
        // Format: @-7.123456,112.123456
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $link, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }
        
        // Format: q=-7.123456,112.123456
        if (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $link, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }
        
        // Format: place/.../@-7.123456,112.123456
        if (preg_match('/place\/[^\/]+\/@(-?\d+\.\d+),(-?\d+\.\d+)/', $link, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }
        
        return null;
    }

    // Fungsi untuk mendapatkan embed URL yang BENAR dan valid
    function getEmbedMapUrl($link_maps, $nama_kos, $kota) {
        if (empty($link_maps)) {
            // Fallback: cari berdasarkan nama dan kota
            return "https://www.google.com/maps/embed/v1/place?key=AIzaSyB...&q=" . urlencode($nama_kos . ' ' . $kota);
        }
        
        // Coba ambil koordinat
        $coords = getCoordinatesFromMapsLink($link_maps);
        if ($coords) {
            // Jika ada koordinat, gunakan format embed yang valid
            return "https://www.google.com/maps?q={$coords['lat']},{$coords['lng']}&output=embed";
        }
        
        // Jika link maps sudah berupa embed link yang valid
        if (strpos($link_maps, 'output=embed') !== false) {
            return $link_maps;
        }
        
        // Untuk link biasa, konversi ke format embed sederhana
        $clean_link = str_replace('view?', '', $link_maps);
        return $clean_link . (strpos($clean_link, '?') === false ? '?output=embed' : '&output=embed');
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?php echo $data['nama_kos']; ?> - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { padding: 40px !important; }
        
        /* Gambar Utama */
        .img-main-wrapper {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .carousel-item img {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        /* Card Detail */
        .detail-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Sticky Price Card (Sisi Kanan) */
        .sticky-price {
            position: sticky;
            top: 40px;
        }
        .price-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #eee;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .badge-tipe {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .facility-item {
            background: #f0f7ff;
            border-radius: 15px;
            padding: 20px 10px;
            text-align: center;
            height: 100%;
            border: 1px solid #e0eeff;
        }
        .facility-item i { font-size: 1.8rem; color: #0d6efd; margin-bottom: 10px; display: block; }
        .facility-item span { font-size: 0.8rem; font-weight: 500; color: #444; }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .map-container {
            border-radius: 20px;
            overflow: hidden;
            height: 300px;
            border: 1px solid #eee;
        }

        .btn-whatsapp {
            background-color: #25d366;
            color: white;
            border: none;
        }
        .btn-whatsapp:hover { background-color: #1eb956; color: white; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="img-main-wrapper">
                    <div id="kosCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php if(empty($fotos)): ?>
                                <div class="carousel-item active">
                                    <img src="../../assets/img/default-kos.jpg" alt="No Image">
                                </div>
                            <?php else: ?>
                                <?php foreach($fotos as $index => $foto): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="../../uploads/kos/<?php echo $foto; ?>" alt="Foto Kos">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#kosCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#kosCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="detail-card mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="fw-bold text-dark mb-2"><?php echo $data['nama_kos']; ?></h2>
                            <p class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo $data['alamat_lengkap']; ?>, <?php echo $data['kota']; ?></p>
                            <div class="d-flex gap-2">
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge-tipe bg-primary-subtle text-primary">
                                        Kos <?php echo ucfirst($data['tipe_kos']); ?>
                                    </span>

                                    <span class="badge-tipe bg-success-subtle text-success">
                                        <i class="bi bi-door-open me-1"></i><?php echo $data['jumlah_kamar']; ?> Tersedia
                                    </span>

                                    <span class="badge-tipe bg-warning-subtle text-dark">
                                        <i class="bi bi-star-fill text-warning me-1"></i>
                                        <strong><?php echo $rata_rating; ?></strong> 
                                        <span class="text-muted small">(<?php echo $total_ulasan; ?> ulasan)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="section-title">Deskripsi Kos</h5>
                    <p class="text-secondary" style="line-height: 1.8; text-align: justify;">
                        <?php echo nl2br($data['deskripsi']); ?>
                    </p>

                    <h5 class="section-title">Fasilitas Utama</h5>
                    <div class="row g-3">
                        <?php foreach($fasilitas as $f): ?>
                        <div class="col-6 col-md-3">
                            <div class="facility-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span><?php echo trim($f); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h5 class="section-title">Peraturan Kos</h5>
                    <div class="p-3 rounded-4 border bg-light">
                        <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                            <?php 
                            // Bersihkan data: hapus semua newline dan spasi di awal
                            $peraturan = preg_replace('/^[\s\n\r]+/', '', $data['peraturan_kos'] ?? '');
                            $peraturan = trim($peraturan);
                            
                            if (!empty($peraturan)) {
                                // Pisahkan per baris, lalu format ulang dengan nomor urut otomatis
                                $lines = explode("\n", $peraturan);
                                $formatted = [];
                                $nomor = 1;
                                foreach ($lines as $line) {
                                    $line = trim($line);
                                    if (!empty($line)) {
                                        // Hapus nomor yang sudah ada (jika ada), lalu ganti dengan nomor urut baru
                                        $clean_line = preg_replace('/^(\d+\.\s*)/', '', $line);
                                        $formatted[] = $nomor . ". " . $clean_line;
                                        $nomor++;
                                    }
                                }
                                echo nl2br(htmlspecialchars(implode("\n", $formatted)));
                            } else {
                                echo '<em class="text-muted">Belum ada peraturan yang ditambahkan.</em>';
                            }
                            ?>
                        </p>
                    </div>

                    <h5 class="section-title">Area Sekitar</h5>
                    <div class="p-3 rounded-4 border bg-light">
                        <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                            <?php 
                            // Bersihkan data area sekitar
                            $area = preg_replace('/^[\s\n\r]+/', '', $data['area_sekitar_kos'] ?? '');
                            $area = trim($area);
                            
                            if (!empty($area)) {
                                $lines = explode("\n", $area);
                                $formatted = [];
                                $nomor = 1;
                                foreach ($lines as $line) {
                                    $line = trim($line);
                                    if (!empty($line)) {
                                        $clean_line = preg_replace('/^(\d+\.\s*)/', '', $line);
                                        $formatted[] = $nomor . ". " . $clean_line;
                                        $nomor++;
                                    }
                                }
                                echo nl2br(htmlspecialchars(implode("\n", $formatted)));
                            } else {
                                echo '<em class="text-muted">Belum ada informasi area sekitar.</em>';
                            }
                            ?>
                        </p>
                    </div>

                    <h5 class="section-title">
                        <i class="bi bi-map-fill me-2"></i> Lokasi di Maps
                    </h5>
                    <div class="map-container mb-3">
                        <?php
                        $maps_query = urlencode($data['nama_kos'] . ' ' . $data['alamat_lengkap'] . ' ' . $data['kota']);
                        ?>
                        <iframe 
                            width="100%" 
                            height="300" 
                            frameborder="0" 
                            style="border:0; border-radius: 16px;" 
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=<?= $maps_query ?>"
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <div class="text-end">
                        <a href="https://maps.google.com/?q=<?= $maps_query ?>" target="_blank" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-pin-map-fill me-2"></i> Buka di Google Maps
                        </a>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-price">
                    <div class="price-card">
                        <p class="text-muted mb-1">Harga Kos Per-Bulan</p>
                        <h3 class="fw-bold text-primary mb-4">Rp <?php echo number_format($data['harga_per_bulan'], 0, ',', '.'); ?> <span class="fs-6 text-muted fw-normal">/ bulan</span></h3>
                        
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/<?php echo $data['no_hp_kos']; ?>" target="_blank" class="btn btn-whatsapp py-3 rounded-pill fw-bold">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi Pemilik
                            </a>
                            <a href="edit_kos.php?id=<?php echo $data['id_kos']; ?>" class="btn btn-outline-primary py-3 rounded-pill fw-bold">
                                <i class="bi bi-pencil-square me-2"></i> Edit Data Kos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>