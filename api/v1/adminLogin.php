<?php
// Include Medoo
require '../../Database.php';

// Start the session
session_start();

// Function to handle redirection
function redirectTo($url)
{
    echo "<script>location.href='$url';</script>";
    exit;
}

// Check if the request is a POST and is JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw POST data
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if input is valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Invalid JSON format.";
        exit;
    }

    // Get form data from JSON input
    $email = htmlentities(strip_tags(stripslashes($input['email']))) ?? '';
    $password = htmlentities(strip_tags(stripslashes($input['password']))) ?? '';
    $ipAddress = htmlentities(strip_tags(stripslashes($input['ip']))) ?? '';
    $location = $input['location'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
        exit;
    }

    // Fetch user from the database
    $user = $database->get(
        'admins',
        [
            'admin_id',
            'admin_email_address',
            'admin_password',
            'is_first_time',
            'is_active',
            'admin_role',
            'is_admin_verified',
            'admin_first_time_password'
        ],
        [
            'AND' => [
                'admin_email_address' => $email,
                'is_active' => 1 // Assuming 1 means active
            ]
        ]
    );

    // Check if user exists
    if (!$user) {
        echo "Admin not found or is banned.";
        exit;
    }

    // Verify the password using Argon2id
    if (!password_verify($password, $user['admin_password'])) {
        echo "Invalid password.";
        exit;
    }

    // Check if it's the user's first time logging in
    if ($user['is_first_time'] == 1) {
        $adminId = $user['admin_id'];
        redirectTo('newPassword.html?id=' . $adminId);
    }

    // Check admin verification only if:
    // - is_first_time = 0 (not first-time login), AND
    // - admin_first_time_password is null (first-time password is not set)
    if ($user['is_first_time'] == 0 && $user['admin_first_time_password'] === null) {
        if ($user['is_admin_verified'] == 0) {
            echo "Admin not verified. Please verify your email address. Check for email in your inbox or spam folder.";
            exit;
        }
    }

    // Password is correct, start the session
    $_SESSION['admin_id'] = $user['admin_id'];
    $_SESSION['admin_email_address'] = $user['admin_email_address'];
    $_SESSION['admin_role'] = $user['admin_role'];

    // Update login details in the database
    $database->update('admins', [
        'login_ip_address' => $ipAddress,
        'login_location' => $location,
        'last_login_datetime' => date('Y-m-d H:i:s')
    ], [
        'admin_email_address' => $email
    ]);

    // Redirect to pages based on role
    $roles = explode(',', $user['admin_role']); // Split the roles into an array

    if (in_array('master', $roles)) {
        redirectTo('dashboard.php?role=master');
    } elseif (in_array('visa consultant', $roles) || in_array('visa associate', $roles)) {
        redirectTo('dashboard.php?role=visa');
    } elseif (in_array('editor', $roles)) {
        redirectTo('dashboard.php?role=editor');
    } elseif (in_array(
        'accountant',
        $roles
    )) {
        redirectTo('dashboard.php?role=accountant');
    } else {
        echo "Invalid role.";
    }
} else {
    echo "Invalid request method. Please use POST.";
    exit;
}
