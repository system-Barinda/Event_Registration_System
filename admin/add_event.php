<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $errors = [];

    if (empty($name)) $errors[] = "Event name required.";
    if (empty($event_date)) $errors[] = "Event date required.";
    if (empty($location)) $errors[] = "Location required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (name, event_date, location, description) VALUES (:name, :event_date, :location, :description)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            header("Location: events.php");
            exit();
        } catch (PDOException $e) {
            echo "Error occurred. Please try again.";
        }
    } else {
        foreach ($errors as $error) echo "<p style='color:red;'>$error</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
<h2>Add Event</h2>
<form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Date:</label><br>
    <input type="date" name="event_date" required><br><br>
    <label>Location:</label><br>
    <input type="text" name="location" required><br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>
    <button type="submit" name="add">Add Event</button>
</form>
<a href="events.php">Back to Events</a>
</body>
</html>
