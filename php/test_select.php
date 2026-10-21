<?php

include "db.php";

$sql = "SELECT * FROM students WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->execute(["test@example.com"]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    echo "Student found!<br>";
    echo "Name: " . $student["name"] . "<br>";
    echo "Email: " . $student["email"] . "<br>";
    echo "Department: " . $student["department"];
} else {
    echo "Student not found.";
}

?>