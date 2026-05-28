import 'package:mobile_app/models/kos.dart';
import 'package:mobile_app/models/blog.dart';

// ─── KOS DATA ────────────────────────────────────────────────────────────────

final List<Kos> dummyKosList = [
  Kos(
    id: 'k001',
    name: 'Kos Melati Indah',
    location: 'Jl. Melati No. 12, Malang',
    price: 800000,
    rating: 4.8,
    image: 'https://picsum.photos/seed/kos1/400/300',
    imageUrl: 'https://picsum.photos/seed/kos1/400/300',
    type: 'Putri',
    description:
        'Kos putri nyaman dan bersih di lingkungan tenang dekat kampus UB. '
        'Akses mudah ke pusat kota dan fasilitas lengkap tersedia untuk kenyamanan penghuni.',
    facilities: ['WiFi', 'AC', 'Kamar Mandi Dalam', 'Lemari', 'Meja Belajar'],
  ),
  Kos(
    id: 'k002',
    name: 'Kos Barokah Residence',
    location: 'Jl. Soekarno Hatta No. 45, Malang',
    price: 650000,
    rating: 4.5,
    image: 'https://picsum.photos/seed/kos2/400/300',
    imageUrl: 'https://picsum.photos/seed/kos2/400/300',
    type: 'Putra',
    description:
        'Kos putra strategis di kawasan bisnis Malang. Dekat dengan minimarket, '
        'warung makan, dan akses transportasi umum yang mudah.',
    facilities: ['WiFi', 'Parkir Motor', 'Dapur Bersama', 'Lemari', 'Kipas Angin'],
  ),
  Kos(
    id: 'k003',
    name: 'Kos Griya Asri',
    location: 'Jl. Veteran No. 8, Malang',
    price: 1200000,
    rating: 4.9,
    image: 'https://picsum.photos/seed/kos3/400/300',
    imageUrl: 'https://picsum.photos/seed/kos3/400/300',
    type: 'Campur',
    description:
        'Kos modern dengan fasilitas premium, cocok untuk mahasiswa maupun pekerja. '
        'Desain interior minimalis dengan pencahayaan alami yang baik.',
    facilities: ['WiFi', 'AC', 'Kamar Mandi Dalam', 'TV', 'Kulkas', 'Laundry', 'CCTV'],
  ),
  Kos(
    id: 'k004',
    name: 'Kos Permata Bunda',
    location: 'Jl. Gajahmada No. 21, Malang',
    price: 550000,
    rating: 4.2,
    image: 'https://picsum.photos/seed/kos4/400/300',
    imageUrl: 'https://picsum.photos/seed/kos4/400/300',
    type: 'Putri',
    description:
        'Kos putri ekonomis dengan suasana kekeluargaan. Ibu kos ramah dan '
        'lingkungan aman. Cocok untuk mahasiswi dengan budget terbatas.',
    facilities: ['WiFi', 'Dapur Bersama', 'Kamar Mandi Luar', 'Lemari', 'Parkir Sepeda'],
  ),
  Kos(
    id: 'k005',
    name: 'Kos Green House',
    location: 'Jl. Simpang Sulfat No. 3, Malang',
    price: 950000,
    rating: 4.6,
    image: 'https://picsum.photos/seed/kos5/400/300',
    imageUrl: 'https://picsum.photos/seed/kos5/400/300',
    type: 'Putra',
    description:
        'Kos putra dengan konsep eco-living. Taman hijau asri di sekitar bangunan '
        'menciptakan udara segar dan suasana belajar yang kondusif.',
    facilities: ['WiFi', 'AC', 'Kamar Mandi Dalam', 'Taman', 'Parkir Motor', 'Dapur'],
  ),
  Kos(
    id: 'k006',
    name: 'Kos Sejahtera Muda',
    location: 'Jl. Letjen S. Parman No. 17, Malang',
    price: 750000,
    rating: 4.3,
    image: 'https://picsum.photos/seed/kos6/400/300',
    imageUrl: 'https://picsum.photos/seed/kos6/400/300',
    type: 'Campur',
    description:
        'Kos campur dengan sistem keamanan 24 jam. Lokasi dekat dengan pusat '
        'perbelanjaan dan restoran sehingga memudahkan aktivitas sehari-hari.',
    facilities: ['WiFi', 'CCTV', 'Satpam', 'Parkir Mobil', 'Kamar Mandi Dalam', 'AC'],
  ),
  Kos(
    id: 'k007',
    name: 'Kos Harmoni Residence',
    location: 'Jl. Dieng No. 5, Malang',
    price: 1500000,
    rating: 5.0,
    image: 'https://picsum.photos/seed/kos7/400/300',
    imageUrl: 'https://picsum.photos/seed/kos7/400/300',
    type: 'Campur',
    description:
        'Kos mewah setara apartemen mini dengan furnitur lengkap. Tersedia ruang '
        'kerja bersama (co-working space) dan rooftop untuk bersantai.',
    facilities: [
      'WiFi Super Cepat',
      'AC',
      'Kamar Mandi Dalam',
      'Smart TV',
      'Kulkas',
      'Microwave',
      'Co-Working Space',
      'Rooftop',
      'Laundry',
    ],
  ),
  Kos(
    id: 'k008',
    name: 'Kos Mawar Putih',
    location: 'Jl. Sukarno No. 30, Batu, Malang',
    price: 600000,
    rating: 4.4,
    image: 'https://picsum.photos/seed/kos8/400/300',
    imageUrl: 'https://picsum.photos/seed/kos8/400/300',
    type: 'Putri',
    description:
        'Kos putri di kawasan wisata Batu dengan udara pegunungan yang sejuk. '
        'Lingkungan tenang dan aman, jauh dari kebisingan kota.',
    facilities: ['WiFi', 'Pemanas Air', 'Kamar Mandi Dalam', 'Lemari', 'Meja Belajar', 'Dapur'],
  ),
];

// ─── BLOG DATA ───────────────────────────────────────────────────────────────

final List<Blog> dummyBlogList = [
  Blog(
    title: 'Tips Memilih Kos yang Tepat untuk Mahasiswa',
    content: '''Memilih kos yang tepat adalah keputusan penting bagi setiap mahasiswa. '
Ada beberapa faktor yang perlu diperhatikan sebelum memutuskan untuk menyewa sebuah kos.

Pertama, pertimbangkan lokasi. Pilih kos yang dekat dengan kampus atau akses transportasi umum agar mobilitas Anda lebih mudah.

Kedua, sesuaikan harga dengan budget. Pastikan harga sewa kos tidak melebihi 30% dari total pengeluaran bulanan Anda.

Ketiga, periksa fasilitas yang tersedia. Fasilitas seperti WiFi, air panas, dan AC sangat membantu kenyamanan sehari-hari.

Keempat, kenali lingkungan sekitar. Pastikan kos berada di lingkungan yang aman, bersih, dan kondusif untuk belajar.

Kelima, baca ulasan dari penghuni sebelumnya. Pengalaman orang lain bisa menjadi bahan pertimbangan yang sangat berguna.''',
    category: 'Education',
    imageUrl: 'https://picsum.photos/seed/blog1/400/250',
    date: '28 April 2026',
  ),
  Blog(
    title: 'Harga Sewa Kos di Malang Meningkat 15% di 2026',
    content: '''Berdasarkan data terbaru dari Asosiasi Pemilik Kos Malang, harga sewa kos '
di kota Malang mengalami kenaikan rata-rata 15% pada tahun 2026 dibandingkan tahun sebelumnya.

Kenaikan ini dipicu oleh meningkatnya biaya operasional, termasuk tagihan listrik dan air yang naik signifikan pasca kebijakan tarif baru pemerintah.

Kawasan di sekitar Universitas Brawijaya dan Universitas Negeri Malang menjadi zona dengan kenaikan tertinggi, mencapai 20%.

Namun demikian, para pencari kos tetap aktif. Permintaan kos masih sangat tinggi mengingat Malang adalah salah satu kota pelajar terbesar di Jawa Timur.

Para ahli properti menyarankan untuk memesan kos jauh-jauh hari sebelum tahun akademik baru dimulai.''',
    category: 'News',
    imageUrl: 'https://picsum.photos/seed/blog2/400/250',
    date: '25 April 2026',
  ),
  Blog(
    title: 'Cara Efektif Mengelola Keuangan Saat Kos',
    content: '''Hidup di kos merupakan pengalaman pertama bagi banyak mahasiswa dalam '
mengelola keuangan secara mandiri. Berikut adalah beberapa tips yang bisa Anda terapkan.

Buat anggaran bulanan yang rinci. Catat semua sumber pemasukan dan rencana pengeluaran dari awal bulan.

Prioritaskan kebutuhan primer seperti sewa kos, makan, dan transportasi sebelum membeli kebutuhan sekunder.

Manfaatkan promo dan diskon. Banyak aplikasi delivery makanan yang menawarkan promo menarik untuk menghemat pengeluaran makan.

Masak sendiri jika memungkinkan. Memasak di dapur kos bisa menghemat hingga 50% pengeluaran makan dibandingkan selalu membeli di luar.

Pisahkan tabungan sejak awal. Segera sisihkan minimal 10% penghasilan atau uang saku ke rekening tabungan.''',
    category: 'Education',
    imageUrl: 'https://picsum.photos/seed/blog3/400/250',
    date: '20 April 2026',
  ),
  Blog(
    title: 'Turnamen Futsal Antar Kos Se-Malang 2026 Resmi Dibuka',
    content: '''Turnamen Futsal Antar Kos Se-Malang 2026 resmi dibuka pada Sabtu, 18 April '
lalu di GOR Futsal Gajayana, Malang. Sebanyak 32 tim dari berbagai kos di seluruh Malang turut berpartisipasi.

Acara ini diinisiasi oleh komunitas pemuda Malang sebagai ajang silaturahmi sekaligus kompetisi olahraga yang sehat antar penghuni kos.

"Kami ingin membangun rasa kebersamaan antar penghuni kos di Malang. Olahraga adalah media yang paling tepat," ujar ketua panitia, Budi Santoso.

Turnamen berlangsung selama dua minggu dan akan berakhir pada 2 Mei 2026. Tim pemenang akan mendapatkan piala bergilir dan hadiah uang tunai senilai Rp 5.000.000.

Pertandingan final dapat disaksikan secara gratis oleh masyarakat umum.''',
    category: 'Sport',
    imageUrl: 'https://picsum.photos/seed/blog4/400/250',
    date: '18 April 2026',
  ),
  Blog(
    title: 'Regulasi Baru: Pemilik Kos Wajib Daftarkan Unit ke Pemda',
    content: '''Pemerintah Kota Malang mengeluarkan regulasi baru yang mewajibkan seluruh '
pemilik kos untuk mendaftarkan unit mereka ke Dinas Perizinan setempat paling lambat 31 Desember 2026.

Kebijakan ini tertuang dalam Peraturan Walikota Malang Nomor 12 Tahun 2026 tentang Penyelenggaraan Rumah Kos.

Pemilik kos yang tidak mendaftarkan unitnya terancam sanksi administratif berupa denda dan pencabutan izin operasional.

Pendaftaran dapat dilakukan secara online melalui portal resmi Pemkot Malang di malangkota.go.id atau langsung datang ke kantor Dinas Perizinan.

Kebijakan ini bertujuan untuk meningkatkan standar kualitas dan keamanan kos di Kota Malang demi kenyamanan penghuni.''',
    category: 'News',
    imageUrl: 'https://picsum.photos/seed/blog5/400/250',
    date: '15 April 2026',
  ),
];
