<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'user') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's registered events
$stmt = $pdo->prepare("
    SELECT e.*, r.id as registration_id
    FROM events e
    JOIN registrations r ON e.id = r.event_id
    WHERE r.user_id = :user_id
    ORDER BY e.event_date ASC
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$registered_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available events (not registered yet)
$stmt = $pdo->prepare("
    SELECT e.*
    FROM events e
    WHERE e.id NOT IN (
        SELECT event_id FROM registrations WHERE user_id = :user_id
    )
    AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$available_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM registrations WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$total_registrations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()");
$upcoming_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../styles/user-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'user_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon registered">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $total_registrations ?></h3>
                    <p>My Registrations</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon upcoming">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $upcoming_events ?></h3>
                    <p>Upcoming Events</p>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- My Registered Events -->
            <div class="section-card">
                <h2><i class="fas fa-list-check"></i> My Registered Events</h2>
                <?php if (count($registered_events) > 0): ?>
                    <div class="events-list">
                        <?php foreach ($registered_events as $event): ?>
                        <div class="event-item">
                            <div class="event-header">
                                <h3><?= htmlspecialchars($event['name']) ?></h3>
                                <span class="badge badge-registered">Registered</span>
                            </div>
                            <div class="event-details">
                                <p><i class="fas fa-calendar"></i> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></p>
                                <p><i class="fas fa-info-circle"></i> <?= htmlspecialchars($event['description']) ?></p>
                            </div>
                            <div class="event-actions">
                                <a href="unregister.php?id=<?= $event['registration_id'] ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to unregister from this event?')">
                                    <i class="fas fa-times"></i> Unregister
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>You haven't registered for any events yet.</p>
                        <a href="register_event.php" class="btn btn-primary">Browse Events</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Available Events -->
            <div class="section-card">
                <h2><i class="fas fa-calendar-plus"></i> Available Events</h2>
                <?php if (count($available_events) > 0): ?>
                    <div class="events-list">
                        <?php foreach (array_slice($available_events, 0, 3) as $event): ?>
                        <div class="event-item">
                            <div class="event-header">
                                <h3><?= htmlspecialchars($event['name']) ?></h3>
                                <span class="badge badge-available">Available</span>
                            </div>
                            <div class="event-details">
                                <p><i class="fas fa-calendar"></i> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></p>
                                <p><i class="fas fa-info-circle"></i> <?= htmlspecialchars($event['description']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($available_events) > 3): ?>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="register_event.php" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View All Events
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>No upcoming events available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>