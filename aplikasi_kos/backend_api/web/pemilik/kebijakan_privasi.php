<?php
require_once '../../config/auth_check.php'; 
require_once '../../config/database.php';
cekAkses('pemilik');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - MY Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="style_admin.css"> -->
    <link rel="stylesheet" href="../../web/style.css">
    <style>
        .policy-content section { margin-bottom: 25px; }
        .policy-content h5 { font-weight: 700; color: #2c3e50; margin-bottom: 15px; }
        .policy-content p, .policy-content li { color: #555; line-height: 1.6; }
        .link-list a { text-decoration: none; color: #0d6efd; font-weight: 500; }
        .link-list a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h3 class="fw-bold"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Kebijakan Privasi</h3>
            <p class="text-muted">Aplikasi My Kos (Icarus Developer)</p>
        </div>

        <div class="row">
            <div class="col-lg-11">
                <div class="card border-0 shadow-sm p-4 mb-5">
                    <div class="card-body policy-content">
                        
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            Halaman ini digunakan untuk memberi tahu pengunjung dan pengguna aplikasi <strong>MY Kost</strong> (dikembangkan oleh Icarus) mengenai kebijakan kami dalam pengumpulan, penggunaan, dan pengungkapan Informasi Pribadi.
                        </div>

                        <p>Jika Anda memilih untuk menggunakan Layanan kami, maka Anda menyetujui pengumpulan dan penggunaan informasi sehubungan dengan kebijakan ini. Informasi Pribadi yang kami kumpulkan digunakan murni untuk menyediakan dan meningkatkan Layanan. Kami tidak akan menggunakan atau membagikan informasi Anda dengan siapa pun kecuali seperti yang dijelaskan dalam Kebijakan Privasi ini.</p>

                        <hr class="my-4">

                        <section>
                            <h5>1. Pengumpulan dan Penggunaan Informasi</h5>
                            <p>Untuk pengalaman yang lebih baik saat menggunakan Layanan kami, kami mungkin meminta Anda untuk memberikan kami informasi pengenal pribadi tertentu. Aplikasi ini menggunakan layanan pihak ketiga yang dapat mengumpulkan informasi yang digunakan untuk mengidentifikasi Anda. Data yang kami kumpulkan meliputi:</p>
                            <ul>
                                <li><strong>Data Akun & Autentikasi:</strong> Saat Anda masuk menggunakan fitur "Login dengan Google", sistem kami (melalui Firebase Authentication) akan mengumpulkan alamat Email, Nama profil, dan ID Pengguna unik Anda. Data ini hanya digunakan untuk mengelola akses akun Anda ke dalam aplikasi.</li>
                                <li><strong>Data Perangkat & Periklanan:</strong> Kami menggunakan layanan periklanan (Google AdMob) yang dapat mengumpulkan ID Iklan (Advertising ID) perangkat Anda untuk menayangkan iklan yang relevan dan menganalisis performa iklan.</li>
                            </ul>
                            <p class="mt-3">Tautan ke kebijakan privasi penyedia layanan pihak ketiga yang digunakan oleh aplikasi:</p>
                            <ul class="link-list">
                                <li><a href="https://www.google.com/policies/privacy/" target="_blank">Google Play Services</a></li>
                                <li><a href="https://firebase.google.com/support/privacy" target="_blank">Google Analytics for Firebase</a></li>
                                <li><a href="https://firebase.google.com/support/privacy" target="_blank">Firebase Crashlytics</a></li>
                                <li><a href="https://policies.google.com/privacy" target="_blank">Google AdMob</a></li>
                            </ul>
                        </section>

                        <section>
                            <h5>2. Hak Penghapusan Akun dan Data Pengguna</h5>
                            <p>Kami menghargai privasi dan kontrol Anda atas data Anda sendiri. Jika Anda ingin menghapus akun Anda beserta seluruh data pribadi yang terkait (seperti email dan status akun) dari sistem kami, Anda dapat mengajukan permohonan penghapusan data dengan cara:</p>
                            <div class="bg-light p-3 border-start border-primary border-4 rounded">
                                <p class="mb-0">Mengirimkan email kepada kami di <strong>mykos@gmail.com</strong> dengan subjek: <strong>"Permohonan Penghapusan Akun"</strong>.</p>
                            </div>
                            <p class="mt-2">Mohon sertakan alamat email yang Anda gunakan saat mendaftar/login di aplikasi My Kost. Kami akan memproses permintaan Anda dan menghapus data Anda dari database kami dalam waktu maksimal 14 hari kerja.</p>
                        </section>

                        <section>
                            <h5>3. Data Lokasi (Location Data)</h5>
                            <p>Aplikasi My Kost menggunakan layanan peta untuk menampilkan lokasi rumah kost dan fasilitas di sekitarnya yang difokuskan pada area UIN Alauddin Makassar. Kami tidak melacak, mengumpulkan, atau menyimpan jejak lokasi GPS real-time atau riwayat perjalanan Anda di server kami. Semua perhitungan jarak dilakukan secara lokal di perangkat Anda.</p>
                        </section>

                        <section>
                            <h5>4. Log Data</h5>
                            <p>Kami ingin memberi tahu Anda bahwa setiap kali Anda menggunakan Layanan kami, jika terjadi kesalahan (error atau crash) pada aplikasi, kami mengumpulkan data dan informasi melalui produk pihak ketiga (Firebase Crashlytics) di ponsel Anda yang disebut Log Data. Log Data ini dapat mencakup informasi seperti alamat Protokol Internet ("IP") perangkat Anda, nama perangkat, versi sistem operasi, konfigurasi aplikasi, waktu dan tanggal penggunaan, serta statistik diagnostik lainnya.</p>
                        </section>

                        <section>
                            <h5>5. Keamanan</h5>
                            <p>Kami menghargai kepercayaan Anda dalam memberikan Informasi Pribadi Anda kepada kami, oleh karena itu kami berusaha menggunakan cara yang dapat diterima secara komersial untuk melindunginya. Namun, perlu diingat bahwa tidak ada metode transmisi melalui internet atau metode penyimpanan elektronik yang 100% aman dan andal, dan kami tidak dapat menjamin keamanan mutlaknya.</p>
                        </section>

                        <section>
                            <h5>6. Perubahan pada Kebijakan Privasi Ini</h5>
                            <p>Kami dapat memperbarui Kebijakan Privasi kami dari waktu ke waktu. Oleh karena itu, Anda disarankan untuk meninjau halaman ini secara berkala untuk melihat setiap perubahan. Kami akan memberi tahu Anda tentang segala perubahan dengan memperbarui Kebijakan Privasi di halaman ini. Kebijakan ini efektif mulai tanggal aplikasi ini dirilis di platform resmi.</p>
                        </section>

                        <section>
                            <h5>7. Hubungi Kami</h5>
                            <p>Jika Anda memiliki pertanyaan, saran, atau keluhan tentang Kebijakan Privasi kami, jangan ragu untuk menghubungi kami melalui email di: <strong>mykos@gmail.com</strong>.</p>
                        </section>

                        <div class="mt-5 pt-3 border-top text-center">
                            <a href="dashboard.php" class="btn btn-outline-secondary px-4 me-2">Kembali</a>
                            <button onclick="window.print()" class="btn btn-primary px-4"><i class="bi bi-printer me-2"></i>Cetak Dokumen</button>
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