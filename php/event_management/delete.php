<?php
require_once __DIR__ . "/auth_admin.php";
require_once __DIR__ . "/db_mysqli.php";
require_once __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

$id = filter_input(INPUT_POST, "event_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    redirect_message("Invalid event ID.");
}

$stmt = $conn->prepare(
    "SELECT poster FROM managed_events WHERE event_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    redirect_message("Event not found.");
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM managed_events WHERE event_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    remove_poster($event["poster"]);

    redirect_message("Event deleted successfully.");

} catch (Throwable $e) {
    error_log($e->getMessage());
    redirect_message("Could not delete event.");
}
?>