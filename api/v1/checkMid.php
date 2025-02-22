<?php

// Function to send a JSON response
function sendJsonResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
}

// Connect to the database
require '../../Database.php';

// Get the 'mid' parameter from the query string
$mid = isset($_GET['mid']) ? $_GET['mid'] : null;

if (!$mid) {
    sendJsonResponse('error', 'Mid parameter is missing.');
    exit; // Stop execution if 'mid' is not provided
}

// Validate the 'mid' to ensure it is in UUIDv4 format
if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i', $mid)) {
    sendJsonResponse('error', 'Not a master admin');
    exit; // Stop execution if 'mid' is not a valid UUIDv4
}

try {
    // Query the table to check if the admin_id matches and admin_role contains 'master'
    $data = $database->select("admins", 'admin_id', [
        "admin_id" => $mid, // Check for matching admin_id
        "admin_role[~]" => "%master%" // Check if admin_role contains 'master' (comma-separated or single)
    ]);

    if ($data) {
        sendJsonResponse('success', 'Admin found');
    } else {
        sendJsonResponse('error', 'Admin not found');
        header('Location: /404.html'); // Redirect to 404 page if admin is not found
        exit;
    }
} catch (Exception $e) {
    logError($e->getMessage());
    sendJsonResponse('error', 'An error occurred. Please try again later.');
}
