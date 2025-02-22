<?php

session_start();
// Connect to the database
require '../../Database.php';

// Check if 'post_id' is present in the request
if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
    // Sanitize and retrieve the 'post_id' value
    $postId = intval($_GET['post_id']); // Use intval to ensure it's an integer

    // Fetch the data based on the post_id using Medoo
    $visaKind = $database->get("visa_kinds", "visa_kind", [
        "id" => $postId
    ]);

    // Check if data was returned
    if (empty($visaKind)) {
        echo json_encode(["message" => "No data found for the given Post ID."]);
        exit;
    }
}
?>
<form id="editVisaKindForm" novalidate>
    <div class="form-group">
        <label for="visaKind">Visa Type</label>
        <input type="text" class="form-control" id="visaKind" name="visaKind" value="<?= isset($visaKind) ? $visaKind : 'Visa type not found'; ?>" required />
        <div class="invalid-feedback">
            Please enter which type of visa it is.
        </div>
    </div>

    <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">
    <input type="hidden" name="postId" id="postId" value="<?= $postId; ?>">

    <button type="submit" class="btn btn-success">Update</button>
</form>