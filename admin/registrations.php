<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE id=:id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
        header("Location: registrations.php");
        exit();
    } catch (PDOException $e) {
        echo "Error occurred. Please try again.";
        exit();
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT r.id, u.name AS user_name, u.email, e.name AS event_name, e.event_date, e.location
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        JOIN events e ON r.event_id = e.id
        ORDER BY e.event_date ASC
    ");
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error occurred. Please try again.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Registrations</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Event Registrations</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <div class="recent-section">
            <h2>All Event Registrations</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($registrations) > 0): ?>
                            <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td><?= $reg['id'] ?></td>
                                <td><?= htmlspecialchars($reg['user_name']) ?></td>
                                <td><?= htmlspecialchars($reg['email']) ?></td>
                                <td><?= htmlspecialchars($reg['event_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($reg['event_date'])) ?></td>
                                <td><?= htmlspecialchars($reg['location']) ?></td>
                                <td>
                                    <a href="registrations.php?delete=<?= $reg['id'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Delete this registration?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No registrations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>