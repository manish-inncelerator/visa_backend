<?php
// updatePassword.php

// Include the Medoo library
require_once '../../Database.php';

// Check if password is received via POST
if (isset($_POST['password'])) {
    // Sanitize and hash the password
    $password = trim(stripslashes(strip_tags($_POST['password'])));

    // Hash the password using Argon2id
    // Set options for Argon2id password hashing
    $options = [
        'memory_cost' => 1 << 17, // 128 MB
        'time_cost' => 4,         // 4 iterations
        'threads' => 2            // 2 threads
    ];
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, $options);

    // Assuming you have a user ID (you need to replace this with actual user logic)
    $userId = trim(stripslashes(strip_tags($_POST['admin_id'])));



    // Update the password in the database using Medoo
    $update = $database->update('admins', [
        'admin_password' => $hashedPassword,
        'is_first_time' => 0,
        'admin_first_time_password' => null
    ], [
        'admin_id' => $userId
    ]);

    if ($update->rowCount() > 0) {
        // Respond with success
        echo json_encode(['success' => true]);
    } else {
        // Respond with failure
        echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Password not provided.']);
}
