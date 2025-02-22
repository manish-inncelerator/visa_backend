<?php
// api/v1/edit_admin_role.php

// Include necessary files
require_once('../../Database.php');

// Decode the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Get the form data
$adminRoles = $data['adminRole'] ?? []; // Array of selected admin roles
$adminID = $data['adminID'] ?? '';
$postId = $data['postId'] ?? '';

// Validate the input
if (empty($adminRoles)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please select at least one admin role."
    ]);
    exit;
}

// Define valid roles
$validRoles = ['master', 'visa associate', 'visa consultant', 'accountant', 'editor'];

// Validate each role
foreach ($adminRoles as $role) {
    if (!in_array($role, $validRoles)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid role selected: $role."
        ]);
        exit;
    }
}

// Check if the editor role is selected with other roles
if (in_array('editor', $adminRoles) && count($adminRoles) > 1) {
    echo json_encode([
        "status" => "error",
        "message" => "The 'editor' role cannot be combined with other roles. Please select only the 'editor' role."
    ]);
    exit;
}

// Process the selected admin roles
$roles = implode(',', $adminRoles); // Convert array to comma-separated string

try {
    // Fetch current admin roles from the database to check if they are the same
    $currentRoles = $database->get("admins", "admin_role", ["admin_id" => $adminID]);

    // If the new roles are the same as the current roles, skip the update
    if ($currentRoles === $roles) {
        echo json_encode([
            "status" => "no_changes",
            "message" => "The selected roles are the same as the current roles. No changes were made."
        ]);
        exit;
    }

    // Update the database
    $database->update("admins", ["admin_role" => $roles], ["admin_id" => $adminID]);

    // Check if the update was successful by checking if the admin role has been updated
    $updatedRoles = $database->get("admins", "admin_role", ["admin_id" => $adminID]);

    // If the updated roles match the new roles, the update was successful
    if ($updatedRoles === $roles) {
        echo json_encode([
            "status" => "success",
            "message" => "Admin roles updated successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "no_changes",
            "message" => "No changes were made. Please check the admin ID."
        ]);
    }
} catch (Exception $e) {
    // Handle database errors
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred while updating the database: " . $e->getMessage()
    ]);
}
