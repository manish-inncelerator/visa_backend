<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the required fields are provided
if (
    isset($data['pageId']) && !empty($data['pageId']) &&
    isset($data['pageName']) && !empty($data['pageName']) &&
    isset($data['pageDescription']) && !empty($data['pageDescription']) &&
    isset($data['pagePosition']) && !empty($data['pagePosition']) &&
    isset($data['pageSlug']) && !empty($data['pageSlug'])
) {
    // Extract and sanitize data
    $pageId = trim($data['pageId']);
    $pageName = trim($data['pageName']);
    $pageDescription = trim($data['pageDescription']);
    $pageDetails = isset($data['pageDetails']) ? trim($data['pageDetails']) : null; // Ensure this is included
    $pagePosition = trim($data['pagePosition']);
    $pageSlug = trim($data['pageSlug']);
    $admin_id = trim($data['admin_id']);

    // Check if the page exists in the database
    $existingPage = $database->select('pages', ['id'], [
        'id' => $pageId
    ]);

    if (empty($existingPage)) {
        // If the page does not exist, return an error
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Page not found.']);
    } else {
        // Update the page in the database using Medoo
        $update = $database->update('pages', [
            'pageName' => $pageName,
            'pageSlug' => $pageSlug,
            'admin_id' => $admin_id,
            'pageDescription' => $pageDescription,
            'updated_at' => date('Y-m-d H:i:s'), // Update the edited_at timestamp
            'pageDetails' => $pageDetails, // Ensure this is included
            'pagePosition' => $pagePosition
        ], [
            'id' => $pageId // Update the page with the specified ID
        ]);

        // Check if the update was successful
        if ($update) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Page updated successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Error updating page.']);
        }
    }
} else {
    // If required fields are not provided
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide all required fields (pageId, pageName, pageDescription, pagePosition).']);
}
