<?php

session_start();

$enrollId = trim($_POST["enrollId"] ?? "");
$password = trim($_POST["password"] ?? "");
$file = "../json/students.json";

if ($enrollId === "" || $password === "") {
    echo "Please enter Enrollment ID and Password.";
    exit;
}

if (!file_exists($file)) {
    echo "Student data file not found.";
    exit;
}

$jsonData = file_get_contents($file);
$students = json_decode($jsonData, true);

if ($students === null) {
    echo "Error reading student data.";
    exit;
}

$loginSuccessful = false;
$studentName = "";

foreach ($students as $student) {

    if ($student["enrollId"] === $enrollId) {

        if ($student["password"] === $password) {
            $_SESSION["student"] = $student;
            $loginSuccessful = true;
            $studentName = $student["fullName"];
        }

        break;
    }
}

if ($loginSuccessful) {
    echo "Redirecting to dashboard...";
    header("Location: ../html/dashboard.html");
    exit;

} else {

    echo "<h2>Login Failed</h2>";
    echo "<p>Invalid Enrollment ID or Password.</p>";
    echo '<a href="../html/login.html">Try Again</a>';
}

?>