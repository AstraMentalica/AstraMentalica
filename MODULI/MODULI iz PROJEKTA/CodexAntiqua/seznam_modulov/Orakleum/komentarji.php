<?php
header('Content-Type: application/json');

// Preberemo komentarje iz JSON datoteke
$filePath = '../datoteke/orakleum/komentarji.json';

if (!file_exists($filePath)) {
    echo json_encode([]);
    exit;
}

$jsonData = file_get_contents($filePath);
$comments = json_decode($jsonData, true) ?: [];

// Filtriranje samo javno prikazljivih podatkov
$publicComments = array_map(function($comment) {
    return [
        'name' => $comment['name'],
        'message' => $comment['message'],
        'timestamp' => $comment['timestamp']
    ];
}, $comments);

echo json_encode($publicComments);
?>