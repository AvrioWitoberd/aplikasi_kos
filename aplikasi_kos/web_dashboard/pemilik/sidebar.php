<div class="sidebar p-3" id="mobileSidebar">
    <div class="sidebar-header mb-4 mt-2 text-center text-md-start">
        <h3 class="fw-bold text-primary px-2">MyKos</h3>
    </div>
    
    <nav class="nav flex-column">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        
        <a class="nav-link mb-2 <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        
        <a class="nav-link mb-2 <?= ($current_page == 'tambah_kos.php') ? 'active' : '' ?>" href="tambah_kos.php">
            <i class="bi bi-plus-circle me-2"></i> Tambah Kos
        </a>
        
        <a class="nav-link mb-2 <?= ($current_page == 'kelola_kos.php') ? 'active' : '' ?>" href="kelola_kos.php">
            <i class="bi bi-gear me-2"></i> Kelola Kos
        </a>
        
        <a class="nav-link mb-2 <?= ($current_page == 'daftar_kos.php') ? 'active' : '' ?>" href="daftar_kos.php">
            <i class="bi bi-house me-2"></i> Daftar Kos
        </a>

        <a class="nav-link mb-2 <?= ($current_page == 'kebijakan_privasi.php') ? 'active' : '' ?>" href="kebijakan_privasi.php">
            <i class="bi bi-shield-lock-fill me-2"></i> Kebijakan Privasi
        </a>

        <hr class="my-3 mx-2">
        
        <a href="#"
        id="btnLogoutMobile" class="nav-link text-danger fw-medium" data-bs-toggle="modal" data-bs-target="#modalLogout">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
    </nav>
</div>

<!-- Modal Logout -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-warning mb-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Keluar?</h5>
                <p class="text-muted small">Apakah Anda yakin ingin keluar?</p>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-light w-100 rounded-3 py-2" data-bs-dismiss="modal">Batal</button>
                    <a href="../logout.php" class="btn btn-danger w-100 rounded-3 py-2">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>
