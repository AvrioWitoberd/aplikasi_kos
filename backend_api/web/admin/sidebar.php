<div class="sidebar d-flex flex-column p-3">
    <div class="sidebar-header mb-4">
        <h3 class="fw-bold text-primary px-2">MyKos</h3>
    </div>
    
    <nav class="nav flex-column">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        
        <a class="nav-link mb-2 <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-people me-2"></i> Pemilik Kos
        </a>
        
        <a class="nav-link mb-2 <?= ($current_page == 'jumlah_kos.php') ? 'active' : '' ?>" href="jumlah_kos.php">
            <i class="bi bi-house me-2"></i> Jumlah Kos
        </a>
        
        <a class="nav-link mb-2 <?= ($current_page == 'blog.php') ? 'active' : '' ?>" href="blog.php">
            <i class="bi bi-journal-text me-2"></i> Blog
        </a>

        <hr class="my-3 mx-2">
        
        <a href="#" class="nav-link text-danger fw-medium" data-bs-toggle="modal" data-bs-target="#modalLogout">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
    </nav>
</div>

<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="modalLogoutLabel">Konfirmasi Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="fs-5 mb-1">Apakah Anda yakin ingin keluar?</p>
                <p class="text-muted small">Anda harus login kembali untuk mengelola data MyKos.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 py-2 fw-medium" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <a href="../logout.php" class="btn btn-danger px-4 fw-semibold" style="border-radius: 12px;">Ya, Keluar</a>
            </div>
        </div>
    </div>
</div>