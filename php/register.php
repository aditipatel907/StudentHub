<?php

include "db.php";

$firstName = trim($_POST["firstName"] ?? "");
$lastName = trim($_POST["lastName"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$pwd = $_POST["password"] ?? "";
$confirmPwd = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

$errors = [];

/* Check empty fields */
if (
    $firstName == "" || $lastName == "" ||
    $email == "" || $mobile == "" ||
    $pwd == "" || $confirmPwd == "" ||
    $course == "" || $year == "" ||
    $gender == "" || !$terms
) {
    $errors[] = "Please fill all the details.";
}

/* Validate names */
if (
    !preg_match("/^[A-Za-z]+$/", $firstName) ||
    !preg_match("/^[A-Za-z]+$/", $lastName)
) {
    $errors[] = "Please enter valid names.";
}

/* Validate mobile */
if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}

/* Validate password */
if (!preg_match("/^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$/", $pwd)) {
    $errors[] = "Password must be at least 8 characters and contain a letter and a number.";
}

/* Confirm password */
if ($pwd !== $confirmPwd) {
    $errors[] = "Passwords do not match.";
}

/* Validate email */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

/* Show validation errors */
if (!empty($errors)) {

    $message = implode("\n", $errors);

    echo "<script>
            alert(" . json_encode($message) . ");
            history.back();
          </script>";
    exit;
}


/* Combine first name and last name */
$name = $firstName . " " . $lastName;


/* Generate enrollment number automatically */
$lastEnrollment = $conn->query(
    "SELECT MAX(CAST(enrollment_no AS UNSIGNED)) FROM students"
)->fetchColumn();

if ($lastEnrollment === null) {
    $enrollmentNo = 101;
} else {
    $enrollmentNo = $lastEnrollment + 1;
}


/* Hash password */
$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);


/* Insert student into MySQL */
$sql = "INSERT INTO students
        (name, email, department, enrollment_no, password, mobile, year, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

try {

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $name,
        $email,
        $course,
        $enrollmentNo,
        $hashedPassword,
        $mobile,
        $year,
        $gender
    ]);

    echo "<script>
            alert('Registration successful! Your Enrollment No. is $enrollmentNo');
            window.location.href = '../html/login.html';
          </script>";

} catch (PDOException $e) {

    if ($e->errorInfo[1] == 1062) {

        echo "<script>
                alert('This email is already registered.');
                history.back();
              </script>";

    } else {

        echo "Registration failed: " . $e->getMessage();
    }
}

exit;

?>