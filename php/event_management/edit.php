<?php
require_once __DIR__ . "/auth_admin.php";
require_once __DIR__ . "/db_mysqli.php";
require_once __DIR__ . "/helpers.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid event ID.");
}

$stmt = $conn->prepare(
    "SELECT * FROM managed_events WHERE event_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    exit("Event not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $time = $_POST["event_time"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");

    if ($title === "" || $description === "" ||
        $date === "" || $time === "" ||
        $venue === "" || $category === "") {
        $error = "Please fill in all required fields.";
    } else {
        $newPoster = null;

        try {
            $newPoster = save_poster($_FILES["poster"] ?? []);
            $poster = $newPoster ?? $event["poster"];

            $stmt = $conn->prepare(
                "UPDATE managed_events
                 SET title=?, description=?, event_date=?,
                     event_time=?, venue=?, category=?, poster=?
                 WHERE event_id=?"
            );

            $stmt->bind_param(
                "sssssssi",
                $title, $description, $date, $time,
                $venue, $category, $poster, $id
            );

            $stmt->execute();

            if ($newPoster !== null) {
                remove_poster($event["poster"]);
            }

            redirect_message("Event updated successfully.");

        } catch (Throwable $e) {
            if ($newPoster !== null) {
                remove_poster($newPoster);
            }

            error_log($e->getMessage());

            $error = $e instanceof RuntimeException
                ? $e->getMessage()
                : "Could not update event.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Event</title>
<style>
body {
    font-family: Arial, sans-serif;
    max-width: 650px;
    margin: 30px auto;
    padding: 15px;
    background: #f4f6fb;
}
form { background: white; padding: 20px; }
label { display: block; margin-top: 14px; }
input, textarea, select {
    box-sizing: border-box;
    width: 100%;
    padding: 10px;
    margin-top: 5px;
}
button, a {
    display: inline-block;
    margin-top: 18px;
    padding: 10px 14px;
    background: #2457d6;
    color: white;
    border: none;
    text-decoration: none;
    cursor: pointer;
}
img { display: block; max-width: 160px; margin-top: 10px; }
.error { color: #b00020; }
</style>
</head>
<body>

<h1>Edit Event</h1>
<a href="index.php">Back to Events</a>

<?php if ($error): ?>
    <p class="error"><?= h($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Event Title *</label>
<input name="title" maxlength="150" required
       value="<?= h($event["title"]) ?>">

<label>Description *</label>
<textarea name="description" rows="4" required><?= h($event["description"]) ?></textarea>

<label>Event Date *</label>
<input type="date" name="event_date" required
       value="<?= h($event["event_date"]) ?>">

<label>Event Time *</label>
<input type="time" name="event_time" required
       value="<?= h(substr($event["event_time"], 0, 5)) ?>">

<label>Venue *</label>
<input name="venue" maxlength="150" required
       value="<?= h($event["venue"]) ?>">

<label>Category *</label>
<select name="category" required>
<?php
$categories = [
    "Technical", "Non-Technical", "Seminars", "Workshops",
    "Competitions", "Sports", "Cultural", "Hackathon"
];
?>
<?php foreach ($categories as $option): ?>
<option value="<?= h($option) ?>"
    <?= $event["category"] === $option ? "selected" : "" ?>>
    <?= h($option) ?>
</option>
<?php endforeach; ?>
</select>

<label>Replace Poster (optional)</label>

<?php if ($event["poster"]): ?>
<img src="uploads/<?= rawurlencode($event["poster"]) ?>"
     alt="Current poster">
<?php else: ?>
<p>No poster uploaded.</p>
<?php endif; ?>

<input type="file" name="poster"
       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

<p>JPG, PNG, WEBP; maximum 2 MB. Leave empty to keep current poster.</p>

<button type="submit">Update Event</button>
</form>
</body>
</html>