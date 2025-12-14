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
            <a href="register_event.php" class="<?= basename($_SERVER['PHP_SELF']) == 'register_event.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-plus"></i>
                <span>Browse Events</span>
            </a>
        </li>
        <li>
            <a href="my_events.php" class="<?= basename($_SERVER['PHP_SELF']) == 'my_events.php' ? 'active' : '' ?>">
                <i class="fas fa-list-check"></i>
                <span>My Events</span>
            </a>
        </li>
        <li>
            <a href="../auth/logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>