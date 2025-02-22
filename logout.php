<?php
// Start the session
session_start();

// Unset specific session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email_address']);

// Optionally, you can destroy the entire session
session_destroy();

// Redirect to the login page
header('Location: login.html');
exit;
