
<?php
require_once "db_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid student ID."));
    exit;
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM students WHERE student_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $deleted = $stmt->affected_rows;
    $stmt->close();

    $message = $deleted > 0
        ? "Student deleted successfully."
        : "Student record not found.";

    header("Location: index.php?message=" . urlencode($message));
    exit;

} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    header("Location: index.php?message=" .
        urlencode("Failed to delete student. The record may be linked to other data."));
    exit;
}
?>
