
<?php
require_once "auth.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: ../html/login.html");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    exit("Access denied. Admins only.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin <?= htmlspecialchars($_SESSION["name"]) ?>!</h1>

    <p>You have administrator access.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
