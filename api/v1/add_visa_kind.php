<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the visa type is provided
if (isset($data['visaKind']) && !empty($data['visaKind'])) {
    // Get the visa type from the decoded JSON data
    $visaKind = trim($data['visaKind']);

    // Check if adminID is provided (it may be optional)
    $adminID = isset($data['adminID']) ? $data['adminID'] : null;

    // Check if the visa type already exists in the database
    $existingVisaType = $database->select('visa_kinds', ['visa_kind'], [
        'visa_kind' => $visaKind
    ]);

    if (!empty($existingVisaType)) {
        // If visa type already exists, return an error
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Visa kind already exists.']);
    } else {
        // Insert the data into the database using Medoo
        $insert = $database->insert('visa_kinds', [
            'visa_kind' => $visaKind,
            'is_active' => 1,
            'admin_id' => $adminID  // Insert adminID if provided
        ]);

        // Check if the insert was successful
        if ($insert) {
            http_response_code(201); // Created
            echo json_encode(['success' => true, 'message' => 'Visa kind added successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Error adding visa kind.']);
        }
    }
} else {
    // If the visa type is not provided
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide a visa kind.']);
}
