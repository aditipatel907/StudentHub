
<?php
require_once "db.php";
require_once "auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/login.html");
    exit;
}

$enrollId = trim($_POST["enrollId"] ?? "");
$password = $_POST["password"] ?? "";

if ($enrollId === "" || $password === "") {
    exit("Please enter Enrollment ID and Password.");
}

$stmt = $conn->prepare(
    "SELECT student_id, name, email, enrollment_no, password, role
     FROM students
     WHERE enrollment_no = ?"
);

$stmt->execute([$enrollId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    !$student ||
    !password_verify($password, $student["password"])
) {
    http_response_code(401);
    exit(
        'Invalid Enrollment ID or Password. ' .
        '<a href="../html/login.html">Try Again</a>'
    );
}

// Prevent session fixation after successful login.
session_regenerate_id(true);

$_SESSION["student_id"] = $student["student_id"];
$_SESSION["name"] = $student["name"];
$_SESSION["email"] = $student["email"];
$_SESSION["enrollId"] = $student["enrollment_no"];
$_SESSION["role"] = $student["role"];
$_SESSION["last_activity"] = time();

if ($student["role"] === "admin") {
    header("Location: admin_dashboard.php");
} else {
    header("Location: dashboard.php");
}

exit;
?>
