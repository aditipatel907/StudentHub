
<?php
require_once "auth.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: ../html/login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?>!</h1>

    <p>
        Enrollment ID:
        <?= htmlspecialchars((string) $_SESSION["enrollId"]) ?>
    </p>

    <p>
        Role:
        <?= htmlspecialchars($_SESSION["role"]) ?>
    </p>

    <p>You are successfully logged in.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
