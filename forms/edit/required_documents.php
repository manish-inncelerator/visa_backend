<?php

session_start();
// Connect to the database
require '../../Database.php';

// Check if 'post_id' is present in the request
if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
    // Sanitize and retrieve the 'post_id' value
    $postId = intval($_GET['post_id']); // Use intval to ensure it's an integer

    // Fetch the data based on the post_id using Medoo
    $required_documents = $database->get("required_documents", "required_document_name", [
        "id" => $postId
    ]);

    // Check if data was returned
    if (empty($required_documents)) {
        echo json_encode(["message" => "No data found for the given Post ID."]);
        exit;
    }
}
?>
<form id="editRequiredDocumentForm" novalidate>
    <div class="form-group">
        <label for="requiredDocumentName">Required Document</label>
        <input type="text" class="form-control" id="requiredDocumentName" name="requiredDocumentName" value="<?= isset($required_documents) ? $required_documents : 'Visa type not found'; ?>" required />
        <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
        <input type="hidden" name="postId" id="postId" value="<?= $postId; ?>">

        <div class="invalid-feedback">
            Please enter a name for required document.
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>