<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MyKos - Temukan Kos Impianmu</title>

    <meta name="description" content="Platform digital pencarian kos yang memudahkan pengguna menemukan hunian terbaik dengan cepat dan mudah.">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <link rel="stylesheet" href="landing/css/landing.css">
</head>
<body>
<div class="hero-bg-shape"></div>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            MyKos
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="#beranda">Beranda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#fitur">Fitur</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#tentang">Tentang</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#faq">FAQ</a>
                </li>

            </ul>

            <div class="d-flex gap-2">

                <a href="#download" class="btn btn-primary">
                    Download App
                </a>

                <a href="web_dashboard/login.php"
                   class="btn btn-outline-primary">
                    Masuk Sistem
                </a>

            </div>

        </div>

    </div>
</nav>

<!-- HERO -->
<section id="beranda" class="hero-section">

    <div class="container">

        <div class="row align-items-center min-vh-100">

            <div class="col-lg-6" data-aos="fade-right">

                <span class="badge bg-primary-subtle text-primary mb-3">
                    Platform Digital Pencarian Kos
                </span>

                <h1 class="hero-title">
                    Temukan Kos Impianmu Dengan Mudah dan Cepat
                </h1>

                <p class="hero-text">
                    Cari kos berdasarkan lokasi, harga,
                    fasilitas, dan ulasan pengguna
                    langsung dari smartphone Anda.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">

                    <a href="#download"
                       class="btn btn-primary btn-lg">
                        Download APK
                    </a>

                    <a href="web_dashboard/login.php"
                       class="btn btn-outline-primary btn-lg">
                        Masuk Sistem
                    </a>

                </div>

            </div>

            <div class="col-lg-6 text-center" data-aos="fade-left">

                <img src="landing/images/hero-phone.png"
                     class="img-fluid hero-image"
                     alt="MyKos App">

            </div>

        </div>

    </div>

</section>

<!-- STATISTIK -->
<section class="stats-section">

    <div class="container">

        <div class="row g-4">

            <div class="col-md-3">
                <div class="stat-card">
                    <h2>500+</h2>
                    <p>Kos Terdaftar</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <h2>100+</h2>
                    <p>Pemilik Kos</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <h2>2000+</h2>
                    <p>Pengguna</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <h2>4.8★</h2>
                    <p>Rating</p>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- TENTANG -->
<section id="tentang" class="section">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">
                <img src="landing/images/about-app.png"
                     class="img-fluid rounded-4 shadow">
            </div>

            <div class="col-lg-6">

                <h2>Tentang MyKos</h2>

                <p>
                    MyKos merupakan platform digital
                    yang membantu mahasiswa, pekerja,
                    dan perantau menemukan tempat tinggal
                    yang sesuai kebutuhan mereka.
                </p>

                <p>
                    Selain itu, MyKos juga menyediakan
                    sistem manajemen bagi pemilik kos
                    untuk mengelola properti secara digital.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- FITUR -->
<section id="fitur" class="section bg-light">

<div class="container">

<h2 class="section-title">
Fitur Unggulan
</h2>

<div class="row g-4">

<?php

$features = [
["bi-search","Cari Kos"],
["bi-house-door","Detail Lengkap"],
["bi-geo-alt","Google Maps"],
["bi-star","Rating & Review"],
["bi-heart","Favorit"],
["bi-whatsapp","WhatsApp"]
];

foreach($features as $feature):

?>

<div class="col-md-4">

<div class="feature-card">

<i class="bi <?= $feature[0] ?>"></i>

<h5><?= $feature[1] ?></h5>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</section>

<!-- SCREENSHOT -->
<section class="section">

<div class="container">

<h2 class="section-title">
Tampilan Aplikasi
</h2>

<div class="swiper mySwiper">

<div class="swiper-wrapper">

<?php

$screenshots = [
"screen-home.png",
"screen-detail.png",
"screen-blog.png",
"screen-favorit.png",
"screen-profile.png"
];

foreach($screenshots as $img):

?>

<div class="swiper-slide">

<img src="landing/images/<?= $img ?>"
     class="img-fluid app-screen">

</div>

<?php endforeach; ?>

</div>

<div class="swiper-pagination"></div>

</div>

</div>

</section>

<!-- PEMILIK KOS -->
<section class="owner-section">

<div class="container text-center">

<h2>
Punya Kos?
</h2>

<p>
Kelola properti Anda dengan mudah
melalui sistem MyKos.
</p>

<a href="web_dashboard/login.php"
   class="btn btn-light btn-lg">
    Masuk Sistem
</a>

</div>

</section>

<!-- FAQ -->
<section id="faq" class="section">

<div class="container">

<h2 class="section-title">
FAQ
</h2>

<div class="accordion" id="faqAccordion">

<div class="accordion-item">
<button class="accordion-button"
data-bs-toggle="collapse"
data-bs-target="#faq1">
Apakah MyKos gratis?
</button>
<div id="faq1"
class="accordion-collapse collapse show">
<div class="accordion-body">
Ya, MyKos dapat digunakan secara gratis.
</div>
</div>
</div>

<div class="accordion-item">
<button class="accordion-button collapsed"
data-bs-toggle="collapse"
data-bs-target="#faq2">
Bagaimana menghubungi pemilik kos?
</button>
<div id="faq2"
class="accordion-collapse collapse">
<div class="accordion-body">
Melalui WhatsApp yang tersedia di detail kos.
</div>
</div>
</div>

</div>

</div>

</section>

<!-- DOWNLOAD -->
<section id="download" class="download-section">

<div class="container text-center">

<h2>
Siap Menemukan Kos Impianmu?
</h2>

<p>
Download aplikasi MyKos sekarang.
</p>

<a href="#"
class="btn btn-light btn-lg">
APK Segera Hadir
</a>

</div>

</section>

<footer>

<div class="container">

<p class="mb-0">
© <?= date('Y') ?> MyKos
</p>

</div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script src="landing/js/landing.js"></script>

</body>
</html>