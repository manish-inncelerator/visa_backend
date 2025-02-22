<?php
session_start();
// Connect to the database
require '../../Database.php';

// Check if 'post_id' is present in the request
if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
    // Sanitize and retrieve the 'post_id' value
    $postId = intval($_GET['post_id']); // Use intval to ensure it's an integer

    // Fetch the admin's roles from the database based on post_id
    $adminRole = $database->get("admins", "admin_role", [
        "admin_id" => $postId
    ]);

    // Default to an empty string if no roles are found
    $adminRole = $adminRole ?? '';
} else {
    // Handle the case where 'post_id' is not provided
    echo json_encode(["message" => "Post ID is missing."]);
    exit;
}
?>

<!-- Add Tabs -->
<ul class="nav nav-tabs mb-3" id="editAdminTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="change-role-tab" data-toggle="tab" href="#changeRole" role="tab" aria-controls="changeRole" aria-selected="true">Change Role</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="change-password-tab" data-toggle="tab" href="#changePassword" role="tab" aria-controls="changePassword" aria-selected="false">Change Password</a>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Change Role Tab -->
    <div class="tab-pane fade show active" id="changeRole" role="tabpanel" aria-labelledby="change-role-tab">
        <form id="editAdminRole" novalidate>
            <div class="form-group">
                <p class="card-text text-muted">Choose additional role for the admin.</p>

                <!-- Display original roles as disabled checkboxes -->
                <div class="mb-3">
                    <?php
                    // Split the admin roles into an array
                    $selectedRoles = !empty($adminRole) ? explode(',', $adminRole) : [];

                    // Loop through selected roles and display them as disabled checkboxes
                    foreach ($selectedRoles as $role) {
                        echo "
                        <div class='form-check custom-checkbox'>
                            <input class='form-check-input custom-checkbox-input' type='checkbox' name='adminRole[]' id='$role' value='$role' checked readonly> 
                            <label class='form-check-label custom-checkbox-label' for='$role'>$role</label>
                            <span class='badge bg-primary text-white'>Original Role</span>
                        </div>
                        ";
                    }

                    // If no roles are selected, display a message
                    if (empty($selectedRoles)) {
                        echo "<span class='text-muted'>No roles assigned.</span>";
                    }
                    ?>
                </div>

                <!-- Checkboxes for adding new roles -->
                <div>
                    <?php
                    // Define all possible roles
                    $allRoles = [
                        'master' => 'Master',
                        'visa associate' => 'Visa Associate',
                        'visa consultant' => 'Visa Consultant',
                        'accountant' => 'Accountant',
                        'editor' => 'Editor',
                    ];

                    // Loop through all roles and generate checkboxes
                    foreach ($allRoles as $roleValue => $roleLabel) {
                        // Skip roles that are already selected (original roles)
                        if (in_array($roleValue, $selectedRoles)) {
                            continue;
                        }

                        echo "
                            <div class='form-check custom-checkbox'>
                                <input class='form-check-input custom-checkbox-input' type='checkbox' name='adminRole[]' id='$roleValue' value='$roleValue'>
                                <label class='form-check-label custom-checkbox-label' for='$roleValue'>$roleLabel</label>
                            </div>
                        ";
                    }
                    ?>
                </div>

                <!-- Validation message -->
                <div class="invalid-feedback">
                    Please select at least one admin role.
                </div>
            </div>

            <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">

            <button type="submit" class="btn btn-success">Update admin role</button>
        </form>
    </div>

    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="changePassword" role="tabpanel" aria-labelledby="change-password-tab">
        <form id="changeAdminPassword" novalidate>
            <!-- Confirmation Message -->
            <div class="alert alert-warning mt-3">
                <strong>Note:</strong> By clicking the button below, you will send a login link with password reset information to the admin. Are you sure you want to continue?
            </div>

            <!-- Hidden Input for Admin ID -->
            <input type="hidden" name="adminID" id="adminID" value="<?= $_SESSION['admin_id']; ?>">

            <!-- Single Button -->
            <button type="submit" class="btn btn-success">Send Password Reset Link</button>
        </form>
    </div>
</div>