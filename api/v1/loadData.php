<?php
session_start();

// Set response header for JSON output
header('Content-Type: application/json');

// Function to send JSON response
function sendJsonResponse($status, $data = null, $message = '')
{
    echo json_encode([
        'status' => $status,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Check if 'load' parameter is set in the URL
if (isset($_GET['load'])) {
    $load = filter_var($_GET['load'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_ID = isset($_GET['aid']) ? filter_var($_GET['aid'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

    if (empty($user_ID)) {
        sendJsonResponse('error', null, 'Invalid user ID.');
    }

    if (!empty($load)) {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $load)) {
            sendJsonResponse('error', null, 'Invalid load parameter.');
        }
        $loadTextify = ucwords(str_replace(['_', '-'], ' ', strip_tags($load)));
    } else {
        sendJsonResponse('error', null, 'Load parameter is empty.');
    }
} else {
    sendJsonResponse('error', null, 'Load parameter is missing.');
}

// Set session user ID
// $_SESSION['user_id'] = 1111;

// Check if the user ID matches the session
if ($user_ID !== (string)$_SESSION['admin_id']) {
    sendJsonResponse('error', null, 'User ID does not match session.');
}

// Connect to the database
require '../../Database.php';

try {
    // Query the table based on $load
    $data = $database->select($load, '*', [
        "ORDER" => ["id" => "DESC"] // or "DESC" for descending order
    ]);
    if ($data) {
        sendJsonResponse('success', $data, 'Data retrieved successfully.');
    } else {
        sendJsonResponse('success', [], 'No data found.');
    }
} catch (Exception $e) {
    logError($e->getMessage());
    sendJsonResponse('error', null, 'An error occurred. Please try again later.');
}

/**
 * Log error messages to a file.
 *
 * @param string $message
 */
function logError($message)
{
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, '../../logs/error.log');
}
