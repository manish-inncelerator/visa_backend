<?php
// Include Medoo
require '../../Database.php';

// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the visa type is provided
if (isset($data['requiredDocumentName']) && !empty($data['requiredDocumentName'])) {
    // Get the visa type from the decoded JSON data
    $requiredDocument = trim($data['requiredDocumentName']);

    // Get the postId (if provided)
    $postId = isset($data['postId']) ? $data['postId'] : null;

    // Check if adminID is provided (it may be optional)
    $adminID = isset($data['adminID']) ? $data['adminID'] : null;

    if ($postId) {
        // Update logic if postId is provided
        $existingrequiredDocument = $database->get('required_documents', '*', ['id' => $postId]);

        if ($existingrequiredDocument) {
            // Update the existing record
            $update = $database->update('required_documents', [
                'required_document_name' => $requiredDocument,
                'admin_id' => $adminID,
                'edited_on' => date('Y-m-d H:i:s') // Add current date and time
            ], ['id' => $postId]);


            if ($update->rowCount() > 0) {
                http_response_code(200); // OK
                echo json_encode(['success' => true, 'message' => 'Visa type updated successfully!']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'message' => 'Error updating visa type.']);
            }
        } else {
            // If no record exists for the provided postId
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Visa type not found.']);
        }
    }
} else {
    // If the visa type is not provided
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please provide a visa type.']);
}
