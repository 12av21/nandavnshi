<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$response = [
    'success' => false,
    'message' => 'Invalid request',
    'data' => null
];

// Enable error logging for debugging
error_log('Validate ID Request: ' . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw POST data for debugging
    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);
    
    // Try to get ID from both form data and JSON body
    $id = trim($_POST['id'] ?? $postData['id'] ?? '');
    
    error_log('Received ID: ' . $id);
    
    if (empty($id)) {
        $response['message'] = 'ID is required';
        error_log('Error: ID is empty');
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check if ID exists in our database first
        $stmt = $pdo->prepare("SELECT name, father_husband_name FROM members WHERE pan_aadhar = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            $response = [
                'success' => false,
                'message' => 'This ID is already registered',
                'data' => null
            ];
        } else {
            // Here you would typically call UIDAI/PAN verification API
            // For now, we'll simulate a response with dummy data
            // Replace this with actual API calls to UIDAI/PAN verification services
            
            // Check if it's a PAN (10 chars, alphanumeric)
            if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i', $id)) {
                // Simulate PAN verification
                $response = [
                    'success' => true,
                    'message' => 'PAN verified',
                    'data' => [
                        'name' => 'Sample Name', // Replace with actual API response
                        'father_name' => 'Father\'s Name' // Replace with actual API response
                    ]
                ];
            } 
            // Check if it's an Aadhar (12 digits)
            elseif (preg_match('/^[2-9]\d{11}$/', $id)) {
                // Simulate Aadhar verification
                $response = [
                    'success' => true,
                    'message' => 'Aadhar verified',
                    'data' => [
                        'name' => 'Sample Name', // Replace with actual API response
                        'father_name' => 'Father\'s Name' // Replace with actual API response
                    ]
                ];
            } else {
                $response['message'] = 'Invalid ID format';
            }
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
