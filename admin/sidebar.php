<div class="sidebar">
    <div class="logo">
        <i class="fas fa-calendar-check"></i>
        <h2>Event Manager</h2>
    </div>
    
    <ul class="nav-menu">
        <li>
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        <li>
            <a href="events.php" class="<?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>
        </li>
        <li>
            <a href="registrations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'registrations.php' ? 'active' : '' ?>">
                <i class="fas fa-ticket-alt"></i>
                <span>Registrations</span>
            </a>
        </li>
        <li>
            <a href="../logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>