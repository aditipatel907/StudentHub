<?php

include "db.php";

$jsonFile = __DIR__ . "/../json/events.json";
$jsonData = file_get_contents($jsonFile);

$events = json_decode($jsonData, true);

if ($events === null) {
    die("Error reading events.json");
}

$eventSql = "INSERT INTO events
(event_id, event_name, description, event_date, venue)
VALUES (?, ?, ?, ?, ?)";

$eventStmt = $conn->prepare($eventSql);

$categorySql = "INSERT INTO event_categories
(event_id, category)
VALUES (?, ?)";

$categoryStmt = $conn->prepare($categorySql);

foreach ($events as $event) {

    $eventStmt->execute([
        $event["id"],
        $event["title"],
        $event["description"],
        $event["date"],
        $event["venue"]
    ]);

    foreach ($event["category"] as $category) {
        $categoryStmt->execute([
            $event["id"],
            $category
        ]);
    }
}

echo "Events and categories imported successfully!";

?>