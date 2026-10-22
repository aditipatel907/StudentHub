<?php
require_once __DIR__ . "/auth_admin.php";
require_once __DIR__ . "/db_mysqli.php";
require_once __DIR__ . "/helpers.php";

$result = $conn->query(
    "SELECT * FROM managed_events
     ORDER BY event_date DESC, event_id DESC"
);

$events = $result->fetch_all(MYSQLI_ASSOC);
$message = $_GET["message"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Management</title>
<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1100px;
    margin: 30px auto;
    padding: 15px;
    background: #f4f6fb;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}
th { background: #e5eaf5; }
img {
    width: 90px;
    max-height: 80px;
    object-fit: cover;
}
a, button {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
}
.add, .edit {
    background: #2457d6;
    color: white;
}
.delete {
    background: #c62828;
    color: white;
}
.message {
    padding: 12px;
    background: #dff5e5;
    margin: 15px 0;
}
</style>
</head>
<body>

<h1>Event Management</h1>

<a class="add" href="add.php">+ Add Event</a>

<?php if ($message !== ""): ?>
    <p class="message"><?= h($message) ?></p>
<?php endif; ?>

<?php if (!$events): ?>
    <p>No events available. Add your first event.</p>
<?php else: ?>

<table>
<thead>
<tr>
    <th>Poster</th>
    <th>Title and Description</th>
    <th>Date</th>
    <th>Time</th>
    <th>Venue</th>
    <th>Category</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>

<?php foreach ($events as $event): ?>
<tr>
    <td>
        <?php if ($event["poster"]): ?>
            <img
                src="uploads/<?= rawurlencode($event["poster"]) ?>"
                alt="Event poster">
        <?php else: ?>
            No poster
        <?php endif; ?>
    </td>

    <td>
        <strong><?= h($event["title"]) ?></strong><br>
        <?= nl2br(h($event["description"])) ?>
    </td>

    <td><?= h($event["event_date"]) ?></td>
    <td><?= h($event["event_time"]) ?></td>
    <td><?= h($event["venue"]) ?></td>
    <td><?= h($event["category"]) ?></td>

    <td>
        <a class="edit"
           href="edit.php?id=<?= (int)$event["event_id"] ?>">
           Edit
        </a>

        <form method="post" action="delete.php"
              onsubmit="return confirm('Delete this event?');">
            <input type="hidden" name="event_id"
                   value="<?= (int)$event["event_id"] ?>">
            <button class="delete" type="submit">Delete</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
<?php endif; ?>

</body>
</html>