<?php
session_start();

// Include the Medoo library
require '../../Database.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$currentPassword = trim(stripslashes(strip_tags($data['currentPassword'])));
$newPassword = trim(stripslashes(strip_tags($data['newPassword'])));

// Assume $adminId is obtained from session or authentication
$adminId = $_SESSION['admin_id']; // Example admin ID

// Retrieve the current password from the database (hash)
$admin = $database->get('admins', ['admin_password'], ['admin_id' => $adminId]);

// Verify the current password
if (!password_verify($currentPassword, $admin['admin_password'])) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit;
}

// Hash the new password using Argon2id
$hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);

// Update the password in the database
$update = $database->update('admins', ['password' => $hashedPassword], ['admin_id' => $adminId]);

// Check if the update was successful
if ($update->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating password']);
}
