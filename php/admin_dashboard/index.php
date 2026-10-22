<?php
require_once __DIR__ . "/../auth.php";

if (
    !isset($_SESSION["student_id"]) ||
    strtolower(trim((string)($_SESSION["role"] ?? ""))) !== "admin"
) {
    http_response_code(403);
    exit("Access denied. Admin login required.");
}

require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/audit.php";

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Record dashboard access.
logAction($conn, "Admin dashboard accessed");

// Count students.
$studentCount = (int)$conn->query(
    "SELECT COUNT(*) FROM students WHERE role = 'student'"
)->fetchColumn();

// Count events from Practical 12's table.
$eventCount = 0;
try {
    $eventCount = (int)$conn->query(
        "SELECT COUNT(*) FROM managed_events"
    )->fetchColumn();
} catch (PDOException $e) {
    // Fallback to the existing events table if managed_events is absent.
    $eventCount = (int)$conn->query(
        "SELECT COUNT(*) FROM events"
    )->fetchColumn();
}

// Count registrations.
$registrationCount = (int)$conn->query(
    "SELECT COUNT(*) FROM registrations"
)->fetchColumn();

// Fetch latest registrations.
// Adjust the selected columns if your registrations schema differs.
$latestRegistrations = [];
try {
    $stmt = $conn->query(
        "SELECT * FROM registrations ORDER BY 1 DESC LIMIT 5"
    );
    $latestRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestRegistrations = [];
}

// Fetch latest audit log entries.
$auditLogs = $conn->query(
    "SELECT user_name, action, details, created_at
     FROM audit_logs
     ORDER BY log_id DESC
     LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

$role = strtolower((string)($_SESSION["role"] ?? "student"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub Admin Dashboard</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6fb;
    color: #263247;
}

header {
    background: #203e83;
    color: white;
    padding: 20px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

header h1 { margin: 0; font-size: 24px; }

header a {
    color: white;
    text-decoration: none;
    margin-left: 15px;
}

nav {
    background: white;
    padding: 15px 5%;
    border-bottom: 1px solid #ddd;
}

nav a {
    color: #203e83;
    text-decoration: none;
    margin-right: 20px;
    display: inline-block;
    padding: 5px 0;
}

main { padding: 25px 5%; }

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 18px;
    margin: 20px 0 30px;
}

.card, .panel {
    background: white;
    border-radius: 9px;
    padding: 22px;
    box-shadow: 0 2px 8px #0000000c;
}

.card h3 {
    color: #657187;
    font-size: 15px;
    margin-top: 0;
}

.number {
    font-size: 32px;
    font-weight: bold;
    color: #203e83;
}

.panel { margin-bottom: 25px; overflow-x: auto; }

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    text-align: left;
    padding: 12px;
    border-bottom: 1px solid #e5e8ef;
}

th { background: #edf1f8; }

.notice {
    background: #e5f5e9;
    padding: 12px;
    border-radius: 6px;
}

@media (max-width: 600px) {
    header a { margin-left: 0; }
    nav a { margin-right: 12px; }
}
</style>
</head>

<body>

<header>
    <div>
        <h1>StudentHub Admin Dashboard</h1>
        <p>Welcome, <?= h($_SESSION["name"] ?? "Admin") ?>!</p>
    </div>

    <div>
        <strong><?= h(ucfirst($role)) ?></strong>
        <a href="../logout.php">Logout</a>
    </div>
</header>

<nav>
    <?php if ($role === "admin"): ?>
        <a href="../admin_dashboard.php">Admin Home</a>
        <a href="../student_management/index.php">Manage Students</a>
        <a href="../event_management/index.php">Manage Events</a>
        <a href="../event_api/index.php">Event API Demo</a>
        <a href="../../html/home.html">StudentHub Home</a>
    <?php else: ?>
        <a href="../../html/dashboard.html">My Dashboard</a>
        <a href="../../html/events.html">Events</a>
    <?php endif; ?>
</nav>

<main>
    <h2>Dashboard Overview</h2>
    <p class="notice">You are viewing the administrator control panel.</p>

    <div class="cards">
        <div class="card">
            <h3>Total Students</h3>
            <div class="number"><?= $studentCount ?></div>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <div class="number"><?= $eventCount ?></div>
        </div>

        <div class="card">
            <h3>Total Registrations</h3>
            <div class="number"><?= $registrationCount ?></div>
        </div>
    </div>

    <section class="panel">
        <h2>Latest Registrations</h2>

        <?php if (!$latestRegistrations): ?>
            <p>No registrations found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <?php foreach (array_keys($latestRegistrations[0]) as $column): ?>
                            <th><?= h(ucwords(str_replace("_", " ", $column))) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestRegistrations as $registration): ?>
                        <tr>
                            <?php foreach ($registration as $value): ?>
                                <td><?= h($value) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Audit Log</h2>
        <p>Recent administrative activity.</p>

        <?php if (!$auditLogs): ?>
            <p>No audit activity recorded.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Date and Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td><?= h($log["user_name"]) ?></td>
                            <td><?= h($log["action"]) ?></td>
                            <td><?= h($log["details"]) ?></td>
                            <td><?= h($log["created_at"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

</body>
</html>