<?php
require_once __DIR__ . "/auth_admin.php";
require_once __DIR__ . "/db_mysqli.php";
require_once __DIR__ . "/helpers.php";

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
        $poster = null;

        try {
            $poster = save_poster($_FILES["poster"] ?? []);

            $stmt = $conn->prepare(
                "INSERT INTO managed_events
                (title, description, event_date, event_time,
                 venue, category, poster)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $title, $description, $date, $time,
                $venue, $category, $poster
            );

            $stmt->execute();

            redirect_message("Event added successfully.");

        } catch (Throwable $e) {
            if ($poster !== null) {
                remove_poster($poster);
            }

            error_log($e->getMessage());

            $error = $e instanceof RuntimeException
                ? $e->getMessage()
                : "Could not add event. Check the entered details.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Event</title>
<style>
body {
    font-family: Arial, sans-serif;
    max-width: 650px;
    margin: 30px auto;
    padding: 15px;
    background: #f4f6fb;
}
form {
    background: white;
    padding: 20px;
}
label {
    display: block;
    margin-top: 14px;
}
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
.error { color: #b00020; }
</style>
</head>
<body>

<h1>Add Event</h1>
<a href="index.php">Back to Events</a>

<?php if ($error): ?>
    <p class="error"><?= h($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Event Title *</label>
<input type="text" name="title" maxlength="150" required>

<label>Description *</label>
<textarea name="description" rows="4" required></textarea>

<label>Event Date *</label>
<input type="date" name="event_date" required>

<label>Event Time *</label>
<input type="time" name="event_time" required>

<label>Venue *</label>
<input type="text" name="venue" maxlength="150" required>

<label>Category *</label>
<select name="category" required>
    <option value="">Select Category</option>
    <option>Technical</option>
    <option>Non-Technical</option>
    <option>Seminars</option>
    <option>Workshops</option>
    <option>Competitions</option>
    <option>Sports</option>
    <option>Cultural</option>
    <option>Hackathon</option>
</select>

<label>Event Poster (optional)</label>
<input type="file" name="poster"
       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

<p>Allowed: JPG, PNG, WEBP. Maximum size: 2 MB.</p>

<button type="submit">Add Event</button>
</form>
</body>
</html>