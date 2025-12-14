<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'user') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$registration_id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($registration_id)) {
    header("Location: my_events.php");
    exit();
}

try {
    // Verify that this registration belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $registration_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Delete the registration
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = :id");
        $stmt->bindParam(':id', $registration_id);
        $stmt->execute();
    }
    
    header("Location: my_events.php");
    exit();
} catch (PDOException $e) {
    echo "Error occurred. Please try again.";
}
?>