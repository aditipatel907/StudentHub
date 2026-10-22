
<?php
require_once "db_mysqli.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $department = $_POST["department"] ?? "";
    $enrollment = trim($_POST["enrollment_no"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $year = (int) ($_POST["year"] ?? 0);
    $gender = $_POST["gender"] ?? "";
    $password = $_POST["password"] ?? "";

    if (
        $name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($department, ["cse", "ece", "it"], true) ||
        $enrollment === "" || $mobile === "" ||
        $year < 1 || $year > 4 ||
        !in_array($gender, ["male", "female", "other"], true) ||
        strlen($password) < 8
    ) {
        $message = "Please enter valid details. Password must be at least 8 characters.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO students
                (name, email, department, enrollment_no, password,
                 mobile, year, gender, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'student')"
            );

            $stmt->bind_param(
                "ssssssis",
                $name, $email, $department, $enrollment,
                $hash, $mobile, $year, $gender
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?message=" .
                urlencode("Student added successfully."));
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $message = "Email or enrollment ID already exists.";
            } else {
                error_log($e->getMessage());
                $message = "Failed to add student.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
</head>
<body>
    <h1>Add Student</h1>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name: <input name="name" required></label><br><br>
        <label>Email: <input type="email" name="email" required></label><br><br>

        <label>Department:
            <select name="department" required>
                <option value="cse">CSE</option>
                <option value="ece">ECE</option>
                <option value="it">IT</option>
            </select>
        </label><br><br>

        <label>Enrollment ID:
            <input name="enrollment_no" required>
        </label><br><br>

        <label>Mobile:
            <input name="mobile" required>
        </label><br><br>

        <label>Year:
            <select name="year" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </label><br><br>

        <label>Gender:
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </label><br><br>

        <label>Initial Password:
            <input type="password" name="password" minlength="8" required>
        </label><br><br>

        <button type="submit">Add Student</button>
    </form>

    <p><a href="index.php">Back to Student List</a></p>
</body>
</html>
<?php $conn->close(); ?>
