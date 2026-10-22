
<?php
require_once "db.php";

// Replace this with the admin account's registered email.
$email = "your-admin-email@example.com";

// Choose a new password for your admin account.
$newPassword = "Admin@12345";

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE students SET password = ? WHERE email = ? AND role = 'admin'"
);

$stmt->execute([$hashedPassword, $email]);

if ($stmt->rowCount() > 0) {
    echo "Admin password reset successfully.";
} else {
    echo "No admin account updated. Check the email and role.";
}
?>
