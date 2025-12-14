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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
    <link rel="stylesheet" href="../styles/user-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'user_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>My Registered Events</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <?php if (count($registered_events) > 0): ?>
            <div class="events-grid">
                <?php foreach ($registered_events as $event): ?>
                <div class="event-card registered">
                    <div class="event-card-header">
                        <h3><?= htmlspecialchars($event['name']) ?></h3>
                        <span class="badge badge-registered">Registered</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-info">
                            <p><i class="fas fa-calendar"></i> <strong>Date:</strong> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <p><i class="fas fa-info-circle"></i> <strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                        </div>
                    </div>
                    <div class="event-card-footer">
                        <a href="unregister.php?id=<?= $event['registration_id'] ?>" 
                           class="btn btn-danger btn-block"
                           onclick="return confirm('Are you sure you want to unregister from this event?')">
                            <i class="fas fa-times"></i> Unregister
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="section-card">
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>You haven't registered for any events yet.</p>
                    <a href="register_event.php" class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i> Browse Events
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .event-card-header {
            background: #3498db;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .event-card-header h3 {
            color: white;
            font-size: 20px;
            flex: 1;
            margin-right: 10px;
        }

        .event-card-body {
            padding: 20px;
        }

        .event-info p {
            margin-bottom: 12px;
            color: #7f8c8d;
            font-size: 14px;
            display: flex;
            align-items: start;
            gap: 10px;
        }

        .event-info i {
            color: #3498db;
            margin-top: 3px;
            min-width: 20px;
        }

        .event-info strong {
            color: #2c3e50;
        }

        .event-card-footer {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #ecf0f1;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }
    </style>
</body>
</html>