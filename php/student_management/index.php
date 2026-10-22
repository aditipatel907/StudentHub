
<?php
require_once "db_mysqli.php";

$message = $_GET["message"] ?? "";
$search = trim($_GET["search"] ?? "");
$department = $_GET["department"] ?? "";
$year = $_GET["year"] ?? "";

$sql = "SELECT student_id, name, email, department,
               enrollment_no, mobile, year, gender
        FROM students
        WHERE 1=1";

$types = "";
$params = [];

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?)";
    $term = "%" . $search . "%";
    $types .= "sss";
    array_push($params, $term, $term, $term);
}

if (in_array($department, ["cse", "ece", "it"], true)) {
    $sql .= " AND department = ?";
    $types .= "s";
    $params[] = $department;
}

if (in_array($year, ["1", "2", "3", "4"], true)) {
    $sql .= " AND year = ?";
    $types .= "i";
    $params[] = (int) $year;
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - StudentHub</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        input, select, button { padding: 8px; margin: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 9px; text-align: left; }
        th { background: #eee; }
        .message { padding: 10px; background: #e7f5e9; margin: 15px 0; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Student Management</h1>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <p><a href="add.php">+ Add New Student</a></p>

    <form method="GET" action="index.php">
        <input
            type="text"
            name="search"
            placeholder="Search name, email or enrollment ID"
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="department">
            <option value="">All Departments</option>
            <option value="cse" <?= $department === "cse" ? "selected" : "" ?>>CSE</option>
            <option value="ece" <?= $department === "ece" ? "selected" : "" ?>>ECE</option>
            <option value="it" <?= $department === "it" ? "selected" : "" ?>>IT</option>
        </select>

        <select name="year">
            <option value="">All Years</option>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>"
                    <?= $year === (string) $i ? "selected" : "" ?>>
                    Year <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>

        <button type="submit">Search / Filter</button>
        <a href="index.php">Clear</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Enrollment ID</th>
                <th>Mobile</th>
                <th>Year</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row["student_id"] ?></td>
                    <td><?= htmlspecialchars($row["name"] ?? "") ?></td>
                    <td><?= htmlspecialchars($row["email"] ?? "") ?></td>
                    <td><?= htmlspecialchars($row["department"] ?? "") ?></td>
                    <td><?= htmlspecialchars($row["enrollment_no"] ?? "") ?></td>
                    <td><?= htmlspecialchars($row["mobile"] ?? "") ?></td>
                    <td><?= (int) $row["year"] ?></td>
                    <td><?= htmlspecialchars($row["gender"] ?? "") ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= (int) $row["student_id"] ?>">Edit</a>
                        |
                        <form method="POST" action="delete.php" style="display:inline"
                              onsubmit="return confirm('Delete this student?');">
                            <input type="hidden" name="id"
                                   value="<?= (int) $row["student_id"] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No student records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
