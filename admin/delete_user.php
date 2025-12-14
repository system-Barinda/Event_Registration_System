<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? '';

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    header("Location: users.php");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header("Location: users.php");
    exit();
} catch (PDOException $e) {
    echo "Error occurred. Please try again.";
}
?>