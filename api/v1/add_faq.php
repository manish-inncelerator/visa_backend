<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the visa type is provided
if (isset($data['faqQuestion']) && !empty($data['faqQuestion']) && isset($data['faqAnswer']) && !empty($data['faqAnswer']) && isset($data['faqCountry']) && !empty($data['faqCountry'])) {
    $faqQuestion = trim($data['faqQuestion']);
    $faqAnswer = trim($data['faqAnswer']);
    $faqCountry = trim($data['faqCountry']);

    // Check if adminID is provided (it may be optional)
    $adminID = isset($data['adminID']) ? $data['adminID'] : null;

    // Check if the visa type already exists in the database
    $existingFaq = $database->select('faq', ['faqQuestion'], [
        'faqQuestion' => $faqQuestion
    ]);

    if (!empty($existingFaq)) {
        // If visa type already exists, return an error
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Faq already exists.']);
    } else {
        // Insert the data into the database using Medoo
        $insert = $database->insert('faq', [
            'faqQuestion' => $faqQuestion,
            'faqAnswer' => $faqAnswer,
            'faqCountry' => $faqCountry,
            'admin_id' => $adminID  // Insert adminID if provided
        ]);

        // Check if the insert was successful
        if ($insert) {
            http_response_code(201); // Created
            echo json_encode(['success' => true, 'message' => 'Faq added successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Error adding Faq.']);
        }
    }
} else {
    // If the visa type is not provided
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide a Faq']);
}
