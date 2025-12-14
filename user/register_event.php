<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'user') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$errors = [];

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

if (isset($_POST['register'])) {
    $event_id = $_POST['event_id'] ?? '';

    if (empty($event_id)) {
        $errors[] = "Please select an event.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id=:user_id AND event_id=:event_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $errors[] = "You have already registered for this event.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (:user_id, :event_id)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':event_id', $event_id);
                $stmt->execute();
                $success = "Registration successful!";
                
                // Refresh available events
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
            }
        } catch (PDOException $e) {
            $errors[] = "Error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events</title>
    <link rel="stylesheet" href="../styles/user-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'user_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Browse Events</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (count($available_events) > 0): ?>
            <div class="events-grid">
                <?php foreach ($available_events as $event): ?>
                <div class="event-card">
                    <div class="event-card-header">
                        <h3><?= htmlspecialchars($event['name']) ?></h3>
                        <span class="badge badge-available">Available</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-info">
                            <p><i class="fas fa-calendar"></i> <strong>Date:</strong> <?= date('F d, Y', strtotime($event['event_date'])) ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <p><i class="fas fa-info-circle"></i> <strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                        </div>
                    </div>
                    <div class="event-card-footer">
                        <form method="POST" action="" style="width: 100%;">
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            <button type="submit" name="register" class="btn btn-primary btn-block">
                                <i class="fas fa-ticket-alt"></i> Register Now
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="section-card">
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p>No upcoming events available at the moment.</p>
                    <p>You have already registered for all available events or there are no events scheduled.</p>
                    <a href="my_events.php" class="btn btn-primary">
                        <i class="fas fa-list-check"></i> View My Events
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
            background: #27ae60;
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
            color: #27ae60;
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