<?php
require_once __DIR__ . "/../auth.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION["student_id"]) ||
    !isset($_SESSION["role"]) ||
    strtolower(trim((string) $_SESSION["role"])) !== "admin"
) {
    http_response_code(403);
    exit("Access denied. Please log in with an admin account.");
}
?>