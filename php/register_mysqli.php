
<?php
require_once "db_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$firstName = trim($_POST["firstName"] ?? "");
$lastName = trim($_POST["lastName"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$pwd = $_POST["password"] ?? "";
$confirmPwd = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = filter_input(INPUT_POST, "year", FILTER_VALIDATE_INT);
$gender = trim($_POST["gender"] ?? "");

if (
    $firstName === "" || $lastName === "" ||
    $email === "" || $mobile === "" ||
    $pwd === "" || $confirmPwd === "" ||
    $course === "" || !$year || $gender === "" ||
    !isset($_POST["terms"])
) {
    exit("Please fill in all required fields.");
}

if (
    !preg_match("/^[A-Za-z]+$/", $firstName) ||
    !preg_match("/^[A-Za-z]+$/", $lastName)
) {
    exit("Please enter valid names.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Please enter a valid email address.");
}

if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    exit("Mobile number must contain exactly 10 digits.");
}

if ($year < 1 || $year > 4) {
    exit("Please select a valid year.");
}

if (!in_array($course, ["cse", "ece", "it"], true)) {
    exit("Please select a valid course.");
}

if (!in_array($gender, ["male", "female", "other"], true)) {
    exit("Please select a valid gender.");
}

if (
    strlen($pwd) < 8 ||
    !preg_match("/[A-Za-z]/", $pwd) ||
    !preg_match("/[0-9]/", $pwd)
) {
    exit("Password must be at least 8 characters and contain a letter and a number.");
}

if ($pwd !== $confirmPwd) {
    exit("Passwords do not match.");
}

$name = $firstName . " " . $lastName;

/* Check duplicate email */
$stmt = $conn->prepare(
    "SELECT student_id FROM students WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    exit("This email is already registered.");
}
$stmt->close();

/* Generate enrollment number */
$result = $conn->query(
    "SELECT MAX(CAST(enrollment_no AS UNSIGNED)) AS last_no
     FROM students"
);
$row = $result->fetch_assoc();
$enrollmentNo = max(100, (int)($row["last_no"] ?? 100)) + 1;

/* Hash password */
$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

/* Insert user with a prepared statement */
$stmt = $conn->prepare(
    "INSERT INTO students
    (name, email, department, enrollment_no, password, mobile, year, gender)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssissis",
    $name,
    $email,
    $course,
    $enrollmentNo,
    $hashedPassword,
    $mobile,
    $year,
    $gender
);

try {
    if ($stmt->execute()) {
        echo "Registration successful! Your Enrollment No. is "
             . htmlspecialchars((string)$enrollmentNo, ENT_QUOTES, "UTF-8");
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        echo "This email or enrollment number is already registered.";
    } else {
        error_log($e->getMessage());
        echo "Registration failed. Please try again.";
    }
}

$stmt->close();
$conn->close();
?>
