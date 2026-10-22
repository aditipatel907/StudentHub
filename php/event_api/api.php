<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

function respond($success, $message, $data = null, $status = 200) {
    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

// Only allow logged-in admins.
if (
    !isset($_SESSION["student_id"]) ||
    strtolower(trim((string)($_SESSION["role"] ?? ""))) !== "admin"
) {
    respond(false, "Admin login required.", null, 403);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "studenthub");
    $conn->set_charset("utf8mb4");

    $method = $_SERVER["REQUEST_METHOD"];

    // LIST EVENTS
    if ($method === "GET") {
        $result = $conn->query(
            "SELECT event_id, title, description, event_date,
                    event_time, venue, category
             FROM managed_events
             ORDER BY event_id DESC"
        );

        respond(true, "Events fetched successfully.",
            $result->fetch_all(MYSQLI_ASSOC));
    }

    // Read JSON sent by Fetch API.
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!is_array($input)) {
        respond(false, "Invalid JSON data.", null, 400);
    }

    // ADD EVENT
    if ($method === "POST") {
        $title = trim($input["title"] ?? "");
        $description = trim($input["description"] ?? "");
        $date = $input["event_date"] ?? "";
        $time = $input["event_time"] ?? "";
        $venue = trim($input["venue"] ?? "");
        $category = trim($input["category"] ?? "");

        if (
            $title === "" || $description === "" ||
            $date === "" || $time === "" ||
            $venue === "" || $category === ""
        ) {
            respond(false, "Please fill in all fields.", null, 400);
        }

        $stmt = $conn->prepare(
            "INSERT INTO managed_events
             (title, description, event_date, event_time,
              venue, category)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssss",
            $title, $description, $date,
            $time, $venue, $category
        );

        $stmt->execute();

        respond(true, "Event added successfully.", [
            "event_id" => $conn->insert_id
        ], 201);
    }

    // UPDATE EVENT
    if ($method === "PUT") {
        $id = filter_var(
            $input["event_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.", null, 400);
        }

        $title = trim($input["title"] ?? "");
        $description = trim($input["description"] ?? "");
        $date = $input["event_date"] ?? "";
        $time = $input["event_time"] ?? "";
        $venue = trim($input["venue"] ?? "");
        $category = trim($input["category"] ?? "");

        if (
            $title === "" || $description === "" ||
            $date === "" || $time === "" ||
            $venue === "" || $category === ""
        ) {
            respond(false, "Please fill in all fields.", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE managed_events
             SET title=?, description=?, event_date=?,
                 event_time=?, venue=?, category=?
             WHERE event_id=?"
        );

        $stmt->bind_param(
            "ssssssi",
            $title, $description, $date,
            $time, $venue, $category, $id
        );

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare(
                "SELECT event_id FROM managed_events WHERE event_id=?"
            );
            $check->bind_param("i", $id);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                respond(false, "Event not found.", null, 404);
            }
        }

        respond(true, "Event updated successfully.");
    }

    // DELETE EVENT
    if ($method === "DELETE") {
        $id = filter_var(
            $input["event_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.", null, 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM managed_events WHERE event_id=?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            respond(false, "Event not found.", null, 404);
        }

        respond(true, "Event deleted successfully.");
    }

    header("Allow: GET, POST, PUT, DELETE");
    respond(false, "Method not allowed.", null, 405);

} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(false, "A server or database error occurred.", null, 500);
}
?>
