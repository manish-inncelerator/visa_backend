<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the required fields are provided
if (
    isset($data['pageName']) && !empty($data['pageName']) &&
    isset($data['pageDescription']) && !empty($data['pageDescription']) &&
    isset($data['pagePosition']) && !empty($data['pagePosition']) &&
    isset($data['pageSlug']) && !empty($data['pageSlug'])
) {

    $pageName = trim($data['pageName']);
    $pageDescription = trim($data['pageDescription']);
    $pageDetails = isset($data['pageDetails']) ? trim($data['pageDetails']) : null;
    $pagePosition = trim($data['pagePosition']);
    $pageSlug = trim($data['pageSlug']);
    $admin_id = trim($data['admin_id']);

    // Check if the page already exists in the database
    $existingPage = $database->select('pages', ['pageName', 'pageSlug'], [
        'pageName' => $pageName,
        'pageSlug' => $pageSlug
    ]);

    if (!empty($existingPage)) {
        // If the page already exists, return an error
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Page already exists.']);
    } else {
        // Insert the data into the database using Medoo
        $insert = $database->insert('pages', [
            'pageName' => $pageName,
            'admin_id' => $admin_id,
            'pageDescription' => $pageDescription,
            'pageSlug' => $pageSlug,
            'pageDetails' => $pageDetails,
            'pagePosition' => $pagePosition
        ]);

        // Check if the insert was successful
        if ($insert) {
            http_response_code(201); // Created
            echo json_encode(['success' => true, 'message' => 'Page added successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Error adding page.']);
        }
    }
} else {
    // If required fields are not provided
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide all required fields (pageName, pageSlug pageDescription, pagePosition).']);
}
