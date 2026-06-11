<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once '../config/database.php';

$id_kos = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id_kos)) {
    echo json_encode(['status' => 'error', 'message' => 'ID Kos tidak ditemukan']);
    exit;
}

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

// Fungsi untuk generate embed map URL dari link pemilik kos
function getEmbedMapFromOwnerLink($link_maps) {
    if (empty($link_maps)) {
        // Jika tidak ada link, gunakan placeholder
        return null;
    }
    
    // Coba ambil koordinat dari link
    $coords = getCoordinatesFromMapsLink($link_maps);
    
    if ($coords) {
        // Jika berhasil ekstrak koordinat, gunakan format koordinat
        return "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={$coords['lat']},{$coords['lng']}";
    }
    
    // Jika link maps sudah berupa embed link yang valid
    if (strpos($link_maps, 'output=embed') !== false) {
        return $link_maps;
    }
    
    // Jika link adalah URL biasa (https://maps.app.goo.gl/... atau https://www.google.com/maps/...)
    // Konversi ke format embed
    if (strpos($link_maps, 'google.com/maps') !== false || strpos($link_maps, 'maps.app.goo.gl') !== false) {
        // Coba ambil parameter q atau ll
        if (preg_match('/[?&]q=([^&]+)/', $link_maps, $matches)) {
            $query = urldecode($matches[1]);
            return "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=" . urlencode($query);
        }
        
        if (preg_match('/[?&]ll=([^&]+)/', $link_maps, $matches)) {
            $ll = $matches[1];
            return "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={$ll}";
        }
        
        // Jika tidak bisa parse, coba redirect dulu untuk shortlink (maps.app.goo.gl)
        // Untuk shortlink, kita akan gunakan sebagai direct link saja, embednya pakai search nama
        return null;
    }
    
    return null;
}

// Fungsi untuk mendapatkan link embed alternatif (menggunakan nama & alamat)
function getEmbedMapFallback($nama_kos, $alamat_lengkap, $kota) {
    $search_query = urlencode($nama_kos . ' ' . $alamat_lengkap . ' ' . $kota);
    return "https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={$search_query}";
}

try {
    // 1. Data Utama Kos
    $query = "SELECT k.*, k.no_hp_kos as no_hp_kos, u.nama_lengkap as nama_pemilik, u.no_hp as no_hp_pemilik 
            FROM kos k 
            JOIN users u ON k.id_pemilik = u.id_user 
            WHERE k.id_kos = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $id_kos]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        exit;
    }

    // 2. Rating & Ulasan
    $query_rating = "SELECT AVG(skor_rating) as rata_rating, COUNT(*) as total_ulasan 
                     FROM rating WHERE id_kos = :id";
    $stmt_rating = $conn->prepare($query_rating);
    $stmt_rating->execute(['id' => $id_kos]);
    $rating_data = $stmt_rating->fetch(PDO::FETCH_ASSOC);

    // 3. Foto-foto
    $query_foto = "SELECT file_nama FROM foto_kos WHERE id_kos = :id";
    $stmt_foto = $conn->prepare($query_foto);
    $stmt_foto->execute(['id' => $id_kos]);
    $fotos = $stmt_foto->fetchAll(PDO::FETCH_COLUMN);

    // 4. Generate embed map URL dari link maps pemilik kos
    $embed_map_url = getEmbedMapFromOwnerLink($data['link_maps'] ?? '');
    
    // Jika gagal dapat embed URL, gunakan fallback berdasarkan nama & alamat
    if (!$embed_map_url) {
        $embed_map_url = getEmbedMapFallback($data['nama_kos'] ?? '', $data['alamat_lengkap'] ?? '', $data['kota'] ?? '');
    }
    
    // 5. Direct link Google Maps (untuk tombol "Buka di Google Maps") - PAKAI LINK PEMILIK KOS
    $direct_map_url = !empty($data['link_maps']) ? $data['link_maps'] : '#';

    // Response Sukses
    echo json_encode([
        'status' => 'success',
        'data' => [
            'kos' => $data,
            'rating' => [
                'rata_rating' => number_format($rating_data['rata_rating'] ?? 0, 1),
                'total_ulasan' => $rating_data['total_ulasan'] ?? 0
            ],
            'fotos' => $fotos,
            'fasilitas' => !empty($data['fasilitas_utama']) ? explode(',', $data['fasilitas_utama']) : [],
            'maps' => [
                'embed_url' => $embed_map_url,
                'direct_url' => $direct_map_url
            ]
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>