<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the visa type is provided
if (
    isset($data['faqQuestion']) && !empty($data['faqQuestion']) &&
    isset($data['faqAnswer']) && !empty($data['faqAnswer']) &&
    isset($data['faqCountry']) && !empty($data['faqCountry'])
) {

    // Get the FAQ data from the decoded JSON data
    $faqQuestion = trim($data['faqQuestion']);
    $faqAnswer = trim($data['faqAnswer']);
    $faqCountry = trim($data['faqCountry']);
    $adminID = isset($data['adminID']) ? $data['adminID'] : null;

    // Get the postId (if provided) for updating
    $postId = isset($data['postId']) ? $data['postId'] : null;

    if ($postId) {
        // Update logic if postId is provided
        $existingFaq = $database->get('faq', '*', ['id' => $postId]);

        if ($existingFaq) {
            // Update the existing FAQ record
            $update = $database->update('faq', [
                'faqQuestion' => $faqQuestion,
                'faqAnswer' => $faqAnswer,
                'faqCountry' => $faqCountry,
                'admin_id' => $adminID,
                'edited_on' => date('Y-m-d H:i:s') // Add current date and time
            ], ['id' => $postId]);

            if ($update->rowCount() > 0) {
                http_response_code(200); // OK
                echo json_encode(['success' => true, 'message' => 'FAQ updated successfully!']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'message' => 'Error updating FAQ.']);
            }
        } else {
            // If no record exists for the provided postId
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'FAQ not found.']);
        }
    }
} else {
    // If any required field is missing
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide all required fields (faqQuestion, faqAnswer, faqCountry).']);
}
