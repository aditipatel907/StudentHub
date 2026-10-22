
<?php
require_once "db_mysqli.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$stmt = $conn->prepare(
    "SELECT name, email, department, enrollment_no, mobile, year, gender
     FROM students WHERE student_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    exit("Student record not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $department = $_POST["department"] ?? "";
    $enrollment = trim($_POST["enrollment_no"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $year = (int) ($_POST["year"] ?? 0);
    $gender = $_POST["gender"] ?? "";

    if (
        $name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($department, ["cse", "ece", "it"], true) ||
        $enrollment === "" || $mobile === "" ||
        $year < 1 || $year > 4 ||
        !in_array($gender, ["male", "female", "other"], true)
    ) {
        $message = "Please enter valid details.";
    } else {
        try {
            $stmt = $conn->prepare(
                "UPDATE students
                 SET name=?, email=?, department=?, enrollment_no=?,
                     mobile=?, year=?, gender=?
                 WHERE student_id=?"
            );

            $stmt->bind_param(
                "sssssisi",
                $name, $email, $department, $enrollment,
                $mobile, $year, $gender, $id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: index.php?message=" .
                urlencode("Student updated successfully."));
            exit;
        } catch (mysqli_sql_exception $e) {
            $message = $e->getCode() === 1062
                ? "Email or enrollment ID already exists."
                : "Failed to update student.";

            error_log($e->getMessage());
        }
    }

    // Keep entered values on validation or database errors.
    $student = [
        "name" => $name,
        "email" => $email,
        "department" => $department,
        "enrollment_no" => $enrollment,
        "mobile" => $mobile,
        "year" => $year,
        "gender" => $gender
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:
            <input name="name" required
                   value="<?= htmlspecialchars($student["name"]) ?>">
        </label><br><br>

        <label>Email:
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($student["email"]) ?>">
        </label><br><br>

        <label>Department:
            <select name="department">
                <?php foreach (["cse", "ece", "it"] as $d): ?>
                    <option value="<?= $d ?>"
                        <?= $student["department"] === $d ? "selected" : "" ?>>
                        <?= strtoupper($d) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Enrollment ID:
            <input name="enrollment_no" required
                   value="<?= htmlspecialchars($student["enrollment_no"]) ?>">
        </label><br><br>

        <label>Mobile:
            <input name="mobile" required
                   value="<?= htmlspecialchars($student["mobile"]) ?>">
        </label><br><br>

        <label>Year:
            <select name="year">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>"
                        <?= (int) $student["year"] === $i ? "selected" : "" ?>>
                        <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label><br><br>

        <label>Gender:
            <select name="gender">
                <?php foreach (["male", "female", "other"] as $g): ?>
                    <option value="<?= $g ?>"
                        <?= $student["gender"] === $g ? "selected" : "" ?>>
                        <?= ucfirst($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="index.php">Back to Student List</a></p>
</body>
</html>
<?php $conn->close(); ?>
