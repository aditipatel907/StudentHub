<?php

$firstName= trim($_POST["firstName"]?? "");
$lastName = trim($_POST["lastName"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$pwd = $_POST["password"] ?? "";
$confirmPwd =  $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);
$errors = [];

if(
    $firstName == "" || $lastName == "" ||
    $email == "" || $mobile == "" ||
    $pwd == "" || $confirmPwd == "" ||
    $course == "" || $year == "" ||
    $gender == "" || !$terms
){
    $errors[] = "Please fill all the details.";
}

if (!preg_match("/^[A-Za-z]+$/", $firstName) ||
    !preg_match("/^[A-Za-z]+$/", $lastName)) {
    $errors[] = "Please enter valid names.";
}

if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}

if (!preg_match("/^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$/", $pwd)) {
    $errors[] = "Password must be at least 8 characters and contain a letter and a number.";
}

if ($pwd !== $confirmPwd) {
    $errors[] = "Passwords do not match.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (!empty($errors)) {
    $message = implode("\n", $errors);

    echo "<script>
            alert(" . json_encode($message) . ");
            history.back();
          </script>";
    exit;
}

$firstName = htmlspecialchars($firstName, ENT_QUOTES, "UTF-8");
$lastName = htmlspecialchars($lastName, ENT_QUOTES, "UTF-8");
$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
$mobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
$course = htmlspecialchars($course, ENT_QUOTES, "UTF-8");
$year = htmlspecialchars($year, ENT_QUOTES, "UTF-8");
$gender = htmlspecialchars($gender, ENT_QUOTES, "UTF-8");

$file = "../json/registrations.json";

$jsonData = file_get_contents($file);
$registrations = json_decode($jsonData, true);
if (!is_array($registrations)) {
    $registrations = [];
}
$regNo = count($registrations) + 1;

$newRegistration = [
    "regNo" => $regNo,
    "firstName" => $firstName,
    "lastName" => $lastName,
    "email" => $email,
    "mobile" => $mobile,
    "password" => password_hash($pwd, PASSWORD_DEFAULT),
    "course" => $course,
    "year" => $year,
    "gender" => $gender
];

$registrations[] = $newRegistration;
$jsonData = json_encode($registrations, JSON_PRETTY_PRINT);
file_put_contents($file, $jsonData);

echo "<script>
        alert('Registration successful!');
        window.location.href = '../html/login.html';
      </script>";
exit;


?>