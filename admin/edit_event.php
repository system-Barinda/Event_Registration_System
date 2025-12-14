<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit();
}

$errors = [];
$success = '';

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    if (empty($name)) $errors[] = "Event name required.";
    if (empty($event_date)) $errors[] = "Event date required.";
    if (empty($location)) $errors[] = "Location required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE events SET name=:name, event_date=:event_date, location=:location, description=:description WHERE id=:id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $success = "Event updated successfully!";
            
            // Refresh event data
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Edit Event</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Edit Event</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </div>

        <div class="form-container">
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success">
                    <p><?= $success ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Event Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Date:</label>
                    <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Location:</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description:</label>
                    <textarea name="description" rows="5"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                    <a href="events.php" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>