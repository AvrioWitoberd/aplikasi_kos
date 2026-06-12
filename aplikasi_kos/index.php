<?php
// MyKos Landing Page
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>MyKos - Temukan Kos Impianmu dengan Cepat dan Mudah</title>
    <meta name="description" content="MyKos adalah platform digital pencarian kos terbaik. Temukan hunian nyaman, aman, dan strategis sesuai kebutuhanmu langsung dari smartphone.">
    <meta name="keywords" content="cari kos, aplikasi kos, sewa kos, kos murah, aplikasi pencarian kos, MyKos">
    <meta name="author" content="MyKos Team">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Swiper JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="landing/css/landing.css">
    
    <style>
        /* Floating WhatsApp Button */
        .float-wa {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .float-wa:hover {
            background-color: #20b858;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 2px 5px 15px rgba(0,0,0,0.25);
        }
    </style>
</head>
<body>

<div class="hero-bg-shape"></div>

<!-- HEADER / NAVBAR -->
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="#" aria-label="Beranda MyKos">
                <i class="bi bi-house-door-fill me-2"></i>MyKos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#faq">FAQ</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 justify-content-center mt-3 mt-lg-0">
                    <a href="download/mykos-v1.0.apk" download="MyKos-v1.0.apk" class="btn btn-primary px-4 rounded-pill" aria-label="Download Aplikasi MyKos">
                        <i class="bi bi-download me-1"></i> Download App
                    </a>
                    <a href="web_dashboard/login.php" class="btn btn-outline-primary px-4 rounded-pill" aria-label="Masuk ke Sistem Dashboard MyKos">
                        Masuk Sistem
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    <!-- HERO SECTION -->
    <section id="beranda" class="hero-section d-flex align-items-center pt-5 mt-5">
        <div class="container">
            <div class="row align-items-center min-vh-75 mt-4">
                <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start" data-aos="fade-right">
                    <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fs-6 d-inline-block shadow-sm">
                        <i class="bi bi-stars me-1"></i> Platform Digital Pencarian Kos #1
                    </span>
                    <h1 class="hero-title display-4 fw-bold text-dark mb-4">
                        Temukan Kos Impianmu Dengan <span class="text-primary">Mudah dan Cepat</span>
                    </h1>
                    <p class="hero-text lead text-secondary mb-4 mx-auto mx-lg-0" style="max-width: 500px;">
                        Cari kos berdasarkan lokasi, harga, fasilitas, dan ulasan pengguna tepercaya langsung dari smartphone Anda. Bebas ribet, aman, dan nyaman.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mt-4">
                        <a href="download/mykos-v1.0.apk" download="MyKos-v1.0.apk" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm" aria-label="Download Aplikasi APK">
                            <i class="bi bi-download me-2"></i> Download APK
                        </a>
                        <a href="web_dashboard/login.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill" aria-label="Masuk sebagai Pemilik Kos">
                            Masuk Sistem <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="200">
                    <img src="landing/images/hero-phone.jpeg" class="img-fluid hero-image rounded-4 shadow-lg" style="max-height: 550px; object-fit: cover;" alt="Aplikasi MyKos di Smartphone">
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTIK SECTION -->
    <section class="stats-section py-5 bg-primary text-white mt-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card p-3 border-end border-light border-opacity-25 h-100">
                        <h2 class="display-5 fw-bold mb-0">500+</h2>
                        <p class="fs-6 mb-0 opacity-75">Kos Terdaftar</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card p-3 border-end-md border-light border-opacity-25 h-100">
                        <h2 class="display-5 fw-bold mb-0">100+</h2>
                        <p class="fs-6 mb-0 opacity-75">Mitra Pemilik</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card p-3 border-end border-light border-opacity-25 h-100">
                        <h2 class="display-5 fw-bold mb-0">2K+</h2>
                        <p class="fs-6 mb-0 opacity-75">Pengguna Aktif</p>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card p-3 h-100">
                        <h2 class="display-5 fw-bold mb-0">4.8<i class="bi bi-star-fill fs-5 text-warning ms-1" style="vertical-align: middle;"></i></h2>
                        <p class="fs-6 mb-0 opacity-75">Rating & Ulasan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG SECTION -->
    <section id="tentang" class="section py-5 my-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right">
                    <div class="position-relative">
                        <img src="landing/images/about-app.png" class="img-fluid rounded-4 shadow-lg w-100" style="object-fit:cover;" alt="Tentang Aplikasi MyKos">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left">
                    <span class="text-primary fw-bold text-uppercase d-block mb-2">Tentang MyKos</span>
                    <h2 class="fw-bold mb-4 display-6">Solusi Terbaik Untuk <span class="text-primary">Pencari</span> dan <span class="text-primary">Pemilik Kos</span></h2>
                    <p class="lead text-secondary mb-4">
                        MyKos hadir sebagai jembatan cerdas yang menghubungkan pencari properti dengan hunian ideal mereka, sekaligus menawarkan sistem manajemen terpadu bagi para pemilik kos.
                    </p>
                    <div class="d-flex align-items-start mb-4 bg-white p-3 rounded-4 shadow-sm border border-light">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <i class="bi bi-search fs-4"></i>
                        </div>
                        <div>
                            <h4 class="h5 fw-bold mb-1">Bagi Pencari Kos</h4>
                            <p class="text-secondary mb-0 small">Temukan kos idaman lebih cepat dengan filter cerdas (harga, lokasi, fasilitas) dan informasi yang transparan.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start bg-white p-3 rounded-4 shadow-sm border border-light">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <div>
                            <h4 class="h5 fw-bold mb-1">Bagi Pemilik Kos</h4>
                            <p class="text-secondary mb-0 small">Kelola ketersediaan kamar, manajemen penyewaan, serta promosikan properti Anda secara gratis kepada ribuan pencari kos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR SECTION -->
    <section id="fitur" class="section py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="text-primary fw-bold text-uppercase">Kelebihan Aplikasi MyKos</span>
                <h2 class="section-title fw-bold mt-2 display-6">Fitur Unggulan Kami</h2>
                <p class="text-secondary mx-auto" style="max-width: 600px;">Nikmati berbagai kemudahan pencarian dan pengelolaan kos dengan fungsionalitas terbaik yang telah kami rancang.</p>
            </div>

            <div class="row g-4">
                <?php
                $features = [
                    ["bi-search", "Pencarian Cerdas", "Saring kos secara spesifik mulai dari jangkauan harga, tipe kos, hingga jarak lokasi."],
                    ["bi-house-door", "Info Kamar Detail", "Cek ketersediaan langsung, foto setiap sudut, hingga list fasilitas yang didapatkan."],
                    ["bi-geo-alt", "Integrasi Maps", "Dilengkapi dengan navigasi lokasi akurat untuk kemudahan survei lokasi secara langsung."],
                    ["bi-star", "Rating & Ulasan", "Lihat testimoni asli dan rating kebersihan dari penghuni kos terdahulu sebelum menyewa."],
                    ["bi-heart", "Simpan Favorit", "Simpan kos andalan yang menjadi wishlist untuk kemudahan perbandingan harga."],
                    ["bi-whatsapp", "Live Chat Pemilik", "Kirim pesan tanpa perantara dan tanya info lebih detail secara real-time via WhatsApp."]
                ];

                foreach($features as $index => $feature):
                    $delay = $index * 100;
                ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <div class="feature-card h-100 p-4 bg-white rounded-4 shadow-sm border border-light position-relative" style="transition: transform 0.3s ease-in-out;">
                        <div class="icon-box bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px;">
                            <i class="bi <?= $feature[0] ?> fs-3"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2"><?= $feature[1] ?></h3>
                        <p class="text-secondary mb-0 small"><?= $feature[2] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SCREENSHOT SECTION -->
    <section class="section py-5 my-4 overflow-hidden">
        <div class="container pb-5 border-bottom border-light-subtle">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title fw-bold display-6">Tampilan Aplikasi</h2>
                <p class="text-secondary">Antarmuka resolusi tinggi yang modern, responsif, intuitif, dan nyaman untuk navigasi.</p>
            </div>

            <div class="swiper mySwiper pb-5" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-wrapper">
                    <?php
                    $screenshots = [
                        "screen-home.jpeg",
                        "screen-detail.jpeg",
                        "screen-blog.jpeg",
                        "screen-favorit.jpeg",
                        "screen-profile.jpeg"
                    ];

                    foreach($screenshots as $img):
                    ?>
                    <div class="swiper-slide d-flex justify-content-center" style="width: 300px;">
                        <img src="landing/images/<?= $img ?>" class="img-fluid app-screen rounded-4 shadow" style="max-height: 550px; object-fit: contain;" alt="Tampilan Antarmuka Aplikasi MyKos">
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination mt-4"></div>
            </div>
        </div>
    </section>

    <!-- PEMILIK KOS SECTION -->
    <section class="owner-section py-5 bg-primary text-white position-relative">
        <div class="container text-center py-5 position-relative z-1">
            <h2 class="fw-bold mb-3 display-6">Punya Properti Kos?</h2>
            <p class="lead mb-5 mx-auto" style="max-width: 700px; color: rgba(255,255,255,0.9);">
                Permudah manajemen kosmu dengan digitalisasi data. Bergabunglah bersama mitra pemilik properti lainnya dan tingkatkan visibilitas hunian Anda sekarang.
            </p>
            <a href="web_dashboard/login.php" class="btn btn-light text-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow" aria-label="Masuk Sistem Manajemen Kos">
                Mulai Kelola Kos <i class="bi bi-box-arrow-in-right ms-2"></i>
            </a>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section id="faq" class="section py-5 my-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title fw-bold display-6">FAQ</h2>
                <p class="text-secondary">Temukan jawaban paling informatif mengenai layanan MyKos.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="accordion accordion-flush bg-white rounded-4 shadow-sm border border-light p-3" id="faqAccordion">

                        <div class="accordion-item border-0 border-bottom">
                            <h3 class="accordion-header" id="headingOne">
                                <button class="accordion-button fw-bold bg-white text-dark py-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                    Apakah menggunakan aplikasi MyKos dipungut biaya?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary pb-4">
                                    Tidak. Bagi Anda pencari kos, penggunaan aplikasi MyKos 100% gratis bebas dari komisi dan biaya langganan bulanan. Anda hanya membayar harga sewa secara langsung pada pemilik.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h3 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-bold bg-white text-dark py-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                    Bagaimana jika saya ingin menghubungi pemilik kos?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary pb-4">
                                    Di lembar spesifikasi setiap properti, kami telah menyematkan tombol ikon WhatsApp. Cukup dengan menekan tombol tersebut, Anda akan langsung disalurkan pada ruang obrolan beserta isi pesan pembuka standar pada pemilik kos.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 border-bottom">
                            <h3 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed fw-bold bg-white text-dark py-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                    Bisakah saya mendaftarkan kos saya ke dalam aplikasi?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary pb-4">
                                    Sangat bisa. Anda dapat mendaftarkan diri dengan menekan tombol "Masuk Sistem" di bagian navbar dan memilih opsi registrasi sebagai pemilik. Setelah diverifikasi, Anda dapat langsung mengunggah foto dan memanajemen properti.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h3 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed fw-bold bg-white text-dark py-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                    Apakah verifikasi identitas (KTP) diperlukan?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary pb-4">
                                    Bagi para pencari kos, data registrasi dasar dirasa cukup untuk masuk sistem penelusuran. Namun bagi para mitra pemilik kos, verifikasi identitas dibutuhkan dengan intensi kenyamanan dan standardisasi keamanan dua arah.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DOWNLOAD SECTION -->
    <section id="download" class="download-section py-5 mb-5">
        <div class="container">
            <div class="bg-light rounded-5 py-5 px-4 text-center shadow-sm position-relative overflow-hidden" data-aos="zoom-in">
                <h2 class="fw-bold mb-3 display-6 mt-3">Siap Menemukan Kos Impianmu?</h2>
                <p class="lead text-secondary mb-5 mx-auto" style="max-width: 600px;">
                    Jadikan MyKos sebagai teman pencarian hunian terpercaya. Unduh aplikasinya sekarang dan rasakan kemudahannya.
                </p>
                <a href="download/mykos-v1.0.apk" download="MyKos-v1.0.apk" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow position-relative z-1 mb-3" aria-label="Download Aplikasi APK">
                    <i class="bi bi-download me-2"></i> Download APK
                </a>
            </div>
        </div>
    </section>

</main>

<!-- FLOATING WHATSAPP BUTTON -->
<a href="https://wa.me/6281234567890" class="float-wa" target="_blank" rel="noopener noreferrer" aria-label="Hubungi Customer Service via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- FOOTER -->
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container pt-4">
        <div class="row mb-4 g-4">
            <div class="col-lg-4 col-md-6 pe-lg-5">
                <h3 class="fw-bold text-white mb-3 d-flex align-items-center">
                    <i class="bi bi-house-door-fill text-primary me-2"></i>MyKos
                </h3>
                <p class="opacity-75 small lh-lg">Platform tata kelola digital properti kos yang memberdayakan pencari hunian dan pemilik kos-kosan dalam satu ekosistem yang kohesif, instan, serta transparan.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="text-white opacity-75 text-decoration-none transition hover-opacity-100" aria-label="Sosial Media Facebook"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-white opacity-75 text-decoration-none transition hover-opacity-100" aria-label="Sosial Media Instagram"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white opacity-75 text-decoration-none transition hover-opacity-100" aria-label="Sosial Media X / Twitter"><i class="bi bi-twitter-x fs-5"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-bold text-white mb-4">Mulai Jelajah</h5>
                <ul class="list-unstyled flex-column d-flex gap-2 small">
                    <li><a href="#beranda" class="text-white opacity-75 text-decoration-none transition hover-opacity-100">Beranda</a></li>
                    <li><a href="#fitur" class="text-white opacity-75 text-decoration-none transition hover-opacity-100">Fitur Utama</a></li>
                    <li><a href="#tentang" class="text-white opacity-75 text-decoration-none transition hover-opacity-100">Cari Tahu Kami</a></li>
                    <li><a href="#faq" class="text-white opacity-75 text-decoration-none transition hover-opacity-100">Bantuan (FAQ)</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-white mb-4">Hubungi Kami</h5>
                <ul class="list-unstyled flex-column d-flex gap-3 small">
                    <li class="d-flex align-items-start">
                        <i class="bi bi-envelope-fill text-primary me-3 fs-5"></i> 
                        <span class="opacity-75 mt-1">support@mykos.com</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-telephone-fill text-primary me-3 fs-5"></i> 
                        <span class="opacity-75 mt-1">+62 89676524908</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-primary me-3 fs-5"></i> 
                        <span class="opacity-75 mt-1">Gedung Pusat Bisnis, Lantai 5<br>Jl. Teknologi No.12, Indonesia</span>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-white mb-4">Partner Pemilik</h5>
                <p class="opacity-75 small lh-lg">Perluas promosi bisnis kos Anda dan catat histori transaksi lebih mudah di dalam dashboard.</p>
                <a href="web_dashboard/login.php" class="btn btn-outline-light btn-sm px-4 py-2 mt-2 rounded-pill" aria-label="Masuk ke Sistem Dashboard Manajamen Kos">
                    Dasbor Pemilik Kos
                </a>
            </div>
        </div>
        
        <hr class="border-secondary mb-4 mt-5 opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50 small">&copy; <?= date('Y') ?> MyKos Application. Hak Cipta Dilindungi.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-white-50 small text-decoration-none me-3 transition hover-opacity-100">Syarat & Ketentuan Aturan</a>
                <a href="#" class="text-white-50 small text-decoration-none transition hover-opacity-100">Kebijakan Privasi Pengguna</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<!-- Custom Script -->
<script src="landing/js/landing.js"></script>

</body>
</html>