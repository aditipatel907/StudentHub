<?php

include "db.php";

$studentId = $_POST["student_id"] ?? "";
$eventId = $_POST["event_id"] ?? "";

if ($studentId == "" || $eventId == "") {
    echo "<script>
            alert('Invalid student or event.');
            history.back();
          </script>";
    exit;
}

$sql = "INSERT INTO registrations
        (student_id, event_id, registration_date)
        VALUES (?, ?, NOW())";

try {

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $studentId,
        $eventId
    ]);

    echo "<script>
            alert('Successfully registered for the event!');
            history.back();
          </script>";

} catch (PDOException $e) {

    if ($e->errorInfo[1] == 1062) {

        echo "<script>
                alert('You are already registered for this event.');
                history.back();
              </script>";

    } else {

        echo "Registration failed: " . $e->getMessage();
    }
}

exit;

?>