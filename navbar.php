<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <button class="btn btn-link" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3">Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="account.php"><i class="bi bi-person"></i> My Account</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="php/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
