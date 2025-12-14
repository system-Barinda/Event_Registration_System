<?php
require_once "../config/db.php";
require_once "../config/session.php";

if ($_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php");
    exit();
}

$errors = [];
$success = '';

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($role)) $errors[] = "Role is required.";

    // Check if email already exists for other users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->fetch()) {
        $errors[] = "Email already exists.";
    }

    if (empty($errors)) {
        try {
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name=:name, email=:email, role=:role, password=:password WHERE id=:id");
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Update without changing password
                $stmt = $pdo->prepare("UPDATE users SET name=:name, email=:email, role=:role WHERE id=:id");
            }
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $success = "User updated successfully!";
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Edit User</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>Edit User</h1>
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
                    <label>Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>New Password (leave blank to keep current):</label>
                    <input type="password" name="password" placeholder="Enter new password">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="users.php" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>