<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-mortarboard-fill"></i> Student Profiling</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>" href="students.php">
                <i class="bi bi-people-fill"></i> Student List
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : ''; ?>" href="account.php">
                <i class="bi bi-person-circle"></i> My Account
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="php/auth/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>
