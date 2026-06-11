<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyKos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style_login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="login-card">
    <div class="logo-container">
        <i class="fas fa-home"></i>
        <h2>MyKos</h2>
        <p>Panel Admin & Pemilik Kos</p>
    </div>

    <div id="alertError" class="alert-box" style="display: none;">
        <i class="fas fa-exclamation-triangle"></i>
        <span id="alertText"></span>
    </div>
    
    <form id="loginForm">
        <div class="input-group">
            <label>Alamat Email</label>
            <div class="input-wrapper">
                <i class="far fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="nama@email.com" required>
            </div>
        </div>

        <div class="input-group">
            <label>Kata Sandi</label>
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-login" id="btnLog">
            <span>Masuk Sekarang</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </form>
</div>

<script>
$('#loginForm').submit(function(e) {
    e.preventDefault();
    
    const alertBox = $('#alertError');
    const alertText = $('#alertText');
    const btn = $('#btnLog');

    // Reset UI & State Loading
    alertBox.hide();
    btn.prop('disabled', true).find('span').text('Memproses...');
    
    $.ajax({
        type: "POST",
        url: "../backend_api/auth/login_web.php", 
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {
            if (res.status == 'success') {
                // User Aktif (Admin/Pemilik) langsung ke Dashboard
                window.location.href = res.role + "/dashboard.php";
            } 
            else if (res.status == 'need_profile') {
                // TANPA MODAL: Langsung lempar ke halaman isi profil
                localStorage.setItem('id_user_temp', res.id_user);
                window.location.href = "pemilik/isi_profil.php";
            } 
            else if (res.status == 'error_message') {
                // Tampilkan pesan error (Password salah/Pending/Nonaktif) di box kuning
                alertText.html(res.message); 
                alertBox.css('display', 'flex').fadeIn();
                btn.prop('disabled', false).find('span').text('Masuk Sekarang');
            }
        },
        error: function(xhr) {
            alertText.text("Gagal terhubung ke server (Error: " + xhr.status + ")");
            alertBox.css('display', 'flex').fadeIn();
            btn.prop('disabled', false).find('span').text('Masuk Sekarang');
        }
    });
});
</script>

</body>
</html>