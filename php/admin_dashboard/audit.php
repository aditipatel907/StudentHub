
<?php
function logAction($conn, $action, $details = "") {
    $userId = $_SESSION["student_id"] ?? null;
    $userName = $_SESSION["name"] ?? "Unknown User";

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, user_name, action, details)
         VALUES (:user_id, :user_name, :action, :details)"
    );

    $stmt->execute([
        ":user_id" => $userId,
        ":user_name" => $userName,
        ":action" => $action,
        ":details" => $details
    ]);
}
?>
