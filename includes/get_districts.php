<?php
// Database connection
require_once __DIR__ . '/../config/database.php';

// Set header to JSON
header('Content-Type: application/json');

// Check if state is provided
if (!isset($_GET['state']) || empty($_GET['state'])) {
    echo json_encode(['error' => 'State not provided']);
    exit;
}

$stateName = $_GET['state'];
$districts = [];

try {
    $stmt = $pdo->prepare("
        SELECT d.name 
        FROM districts d
        JOIN states s ON d.state_id = s.id
        WHERE s.name = ?
        ORDER BY d.name ASC
    ");
    $stmt->execute([$stateName]);
    $districts = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Log error, but return an empty array to the client
    error_log("Get Districts Error: " . $e->getMessage());
    echo json_encode(['error' => 'Could not retrieve districts.']);
    exit;
}

// Return JSON response
echo json_encode(['districts' => $districts]);
?>
