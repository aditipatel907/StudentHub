<?php

session_start();

if (!isset($_SESSION["student"])) {
    echo json_encode(["error" => "Not logged in."]);
    exit;
}

$student = $_SESSION["student"];

echo json_encode($student);

?>