<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Events</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <div class="recent-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>All Events</h2>
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($events) > 0): ?>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= $event['id'] ?></td>
                                <td><?= htmlspecialchars($event['name']) ?></td>
                                <td><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
                                <td><?= htmlspecialchars($event['location']) ?></td>
                                <td><?= htmlspecialchars(substr($event['description'], 0, 50)) ?><?= strlen($event['description']) > 50 ? '...' : '' ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete_event.php?id=<?= $event['id'] ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Delete this event?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No events found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>