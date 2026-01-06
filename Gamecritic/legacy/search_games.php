<?php
header('Content-Type: application/json');
require_once "db/db.php";

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if ($term === '') {
    echo json_encode([]);
    exit;
}


$stmt = $conn->prepare("SELECT id, title FROM games WHERE title ILIKE ? LIMIT 10");
$searchTerm = "%$term%";


$stmt->bind_param("s", $searchTerm);

$stmt->execute();
$result = $stmt->get_result();

$games = [];
while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}

echo json_encode($games);
