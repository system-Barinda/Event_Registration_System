<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? '';

try {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header("Location: events.php");
    exit();
} catch (PDOException $e) {
    echo "Error occurred. Please try again.";
}
