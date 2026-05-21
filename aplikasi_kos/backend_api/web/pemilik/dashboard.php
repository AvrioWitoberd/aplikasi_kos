<?php
require_once '../../config/auth_check.php'; 
require_once '../../config/database.php';
cekAkses('pemilik');

// 2. Sekarang kita bisa langsung pakai variabelnya
$id_pemilik = $_SESSION['user_id'];

// 3. Inisialisasi variabel untuk dashboard
$total_kos = 0;
$total_stok = 0;
$kos_terbaru = [];

// Pastikan variabel $conn tersedia (hasil dari database.php)
if (isset($conn) && $id_pemilik) {
    try {
        // 1. Hitung Total Unit Kos milik pemilik ini
        $stmt1 = $conn->prepare("SELECT COUNT(*) FROM kos WHERE id_pemilik = ?");
        $stmt1->execute([$id_pemilik]);
        $total_kos = $stmt1->fetchColumn() ?: 0;

        // 2. Hitung Total Stok Kamar
        $stmt2 = $conn->prepare("SELECT SUM(jumlah_kamar) FROM kos WHERE id_pemilik = ?");
        $stmt2->execute([$id_pemilik]);
        $total_stok = $stmt2->fetchColumn() ?: 0;

        // 3. Ambil 5 Kos Terbaru
        $stmt3 = $conn->prepare("SELECT nama_kos, tipe_kos, harga_per_bulan FROM kos WHERE id_pemilik = ? ORDER BY id_kos DESC LIMIT 5");
        $stmt3->execute([$id_pemilik]);
        $kos_terbaru = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="style_admin.css"> -->
    <link rel="stylesheet" href="../../web/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h3 class="fw-bold">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Pemilik') ?>! 👋</h3>
            <p class="text-muted">Berikut adalah ringkasan properti kos Anda hari ini.</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-primary text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="fs-1 me-3"><i class="bi bi-houses"></i></div>
                        <div>
                            <h6 class="mb-0">Total Unit Kos</h6>
                            <h2 class="fw-bold mb-0"><?= $total_kos ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-success text-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="fs-1 me-3"><i class="bi bi-door-open"></i></div>
                        <div>
                            <h6 class="mb-0">Total Stok Kamar</h6>
                            <h2 class="fw-bold mb-0"><?= $total_stok ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-warning text-dark h-100">
                    <div class="d-flex align-items-center">
                        <div class="fs-1 me-3"><i class="bi bi-patch-check-fill"></i></div>
                        <div>
                            <h6 class="mb-0">Status Akun</h6>
                            <h4 class="fw-bold mb-0">Pemilik Aktif</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Unit Kos Terbaru</h5>
                        <a href="daftar_kos.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Kos</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($kos_terbaru)) : ?>
                                    <?php foreach($kos_terbaru as $k) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($k['nama_kos']) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= ucfirst($k['tipe_kos']) ?></span></td>
                                        <td>Rp <?= number_format($k['harga_per_bulan'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr><td colspan="3" class="text-center text-muted">Belum ada data kos yang ditambahkan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-light h-100">
                    <h5 class="fw-bold mb-3">Bantuan Cepat</h5>
                    <div class="list-group list-group-flush bg-transparent">
                        <a href="tambah_kos.php" class="list-group-item list-group-item-action bg-transparent border-0 px-0">
                            <i class="bi bi-plus-circle me-2 text-primary"></i> Tambah Unit Baru
                        </a>

                        <a href="#" class="list-group-item list-group-item-action bg-transparent border-0 px-0" data-bs-toggle="modal" data-bs-target="#modalResetAkun">
                            <i class="bi bi-person-gear me-2 text-primary"></i> Reset Email & Password
                        </a>

                        <a href="kebijakan_privasi.php" class="list-group-item list-group-item-action bg-transparent border-0 px-0">
                            <i class="bi bi-shield-check me-2 text-primary"></i> Kebijakan Privasi
                        </a>

                        <a href="https://wa.me/6281332077170?text=Halo%20Admin%20MyKos,%20saya%20ingin%20bertanya..." target="_blank" class="list-group-item list-group-item-action bg-transparent border-0 px-0">
                            <i class="bi bi-headset me-2 text-success"></i> Pusat Bantuan
                        </a>

                        <a href="#" class="list-group-item list-group-item-action bg-transparent border-0 px-0 text-danger" data-bs-toggle="modal" data-bs-target="#modalHapusAkun">
                            <i class="bi bi-trash me-2"></i> Hapus Akun Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalResetAkun" tabindex="-1" aria-labelledby="modalResetAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modalResetAkunLabel">Update Akun Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses_update_akun.php" method="POST">
                <div class="modal-body py-3">
                    <p class="text-muted small mb-4">Silakan isi form di bawah untuk memperbarui data akses masuk Anda.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Baru</label>
                        <input type="email" name="new_email" class="form-control" placeholder="Masukkan email baru" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti password">
                        <div class="form-text small" style="font-size: 0.75rem;">Biarkan kosong jika hanya ingin mengubah email.</div>
                    </div>

                    <hr class="my-4">
                    <div class="bg-light p-3 rounded-3">
                        <label class="form-label small fw-bold text-danger">Konfirmasi Password Lama</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Wajib isi password saat ini" required>
                        <div class="form-text small">Dibutuhkan untuk memvalidasi identitas Anda.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusAkun" tabindex="-1" aria-labelledby="modalHapusAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger" id="modalHapusAkunLabel">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHapusAkun">
                <div class="modal-body py-3">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-octagon text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center fw-bold mb-1">Tindakan ini tidak dapat dibatalkan!</p>
                    <p class="text-muted small text-center mb-4">Seluruh data kos dan profil Anda akan dihapus permanen dari sistem MyKos.</p>

                    <div id="pesanErrorHapus" class="alert alert-danger py-2 small border-0 mb-3 text-center d-none">
                        <i class="bi bi-x-circle me-1"></i> Password yang Anda masukkan salah!
                    </div>
                    
                    <div class="bg-light p-3 rounded-3">
                        <label class="form-label small fw-bold">Masukkan Password Anda</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ketik password untuk hapus" required>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnProsesHapus" class="btn btn-danger px-4 fw-bold">Ya, Hapus Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#formHapusAkun').on('submit', function(e) {
        e.preventDefault(); // Mencegah refresh halaman
        
        const password = $('#confirm_password').val();
        const btn = $('#btnProsesHapus');
        const errorDiv = $('#pesanErrorHapus');

        btn.prop('disabled', true).html('Memproses...');
        errorDiv.addClass('d-none');

        $.ajax({
            url: 'proses_hapus_akun.php',
            type: 'POST',
            data: { confirm_password: password },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Jika sukses, baru pindah halaman ke login
                    window.location.href = '../login.php?msg=akun_dihapus';
                } else {
                    // Jika gagal, tampilkan pesan tanpa refresh
                    errorDiv.removeClass('d-none');
                    btn.prop('disabled', false).html('Ya, Hapus Permanen');
                    $('#confirm_password').val('').focus();
                }
            },
            error: function() {
                alert('Terjadi kesalahan pada server.');
                btn.prop('disabled', false).html('Ya, Hapus Permanen');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>