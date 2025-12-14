<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM events");
$total_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM registrations");
$total_registrations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent registrations
$stmt = $pdo->prepare("
    SELECT r.id, u.name AS user_name, e.name AS event_name, e.event_date
    FROM registrations r
    JOIN users u ON r.user_id = u.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.id DESC
    LIMIT 5
");
$stmt->execute();
$recent_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $total_users ?></h3>
                    <p>Total Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon events">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $total_events ?></h3>
                    <p>Total Events</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon registrations">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $total_registrations ?></h3>
                    <p>Total Registrations</p>
                </div>
            </div>
        </div>

        <div class="recent-section">
            <h2>Recent Registrations</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Event Name</th>
                            <th>Event Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_registrations) > 0): ?>
                            <?php foreach ($recent_registrations as $reg): ?>
                            <tr>
                                <td><?= $reg['id'] ?></td>
                                <td><?= htmlspecialchars($reg['user_name']) ?></td>
                                <td><?= htmlspecialchars($reg['event_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($reg['event_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No registrations yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>