<?php
// saveImage.php

// Ensure the uploads/countries directory exists
$uploadDir = 'uploads/countries/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Sanitize the filename by removing query parameters
    $fileName = basename($_FILES['file']['name']);
    $sanitizedFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName); // Replace invalid characters with underscores
    $filePath = $uploadDir . $sanitizedFileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        echo "Image saved successfully: " . $sanitizedFileName;
    } else {
        echo "Failed to save image.";
    }
} else {
    echo "Invalid request.";
}
