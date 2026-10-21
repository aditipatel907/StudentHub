<?php

include "db.php";

$sql = "INSERT INTO students (name, email, department, password)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$password = password_hash("test1234", PASSWORD_DEFAULT);

$stmt->execute([
    "Test Student",
    "test@example.com",
    "CSE",
    $password
]);

echo "Student inserted successfully!";

?>