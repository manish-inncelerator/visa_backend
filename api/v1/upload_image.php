<?php
// Define the upload directory and allowed file types
$uploadDir = '../../assets/uploads/';
$allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif']; // Allowed MIME types
$maxFileSize = 5 * 1024 * 1024; // Max file size (5 MB)

// Ensure the upload directory exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Function to generate a UUID (v4)
function generateUUIDv4()
{
    // Generate random bytes
    $data = random_bytes(16);

    // Set the version (4) and variant (RFC 4122) in the UUID
    // Set the 6th byte to 0x4 (version 4)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set the 8th byte to 0x80 or higher (variant)
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Convert the bytes to a string in the UUID format
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Check if a file has been uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];

    // Check if the file type is allowed
    if (!in_array($fileType, $allowedFileTypes)) {
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }

    // Check if the file size is within the allowed limit
    if ($fileSize > $maxFileSize) {
        echo json_encode(['error' => 'File size exceeds the maximum limit of 5MB']);
        exit;
    }

    // Generate a unique name for the uploaded file
    $fileNewName = generateUUIDv4() . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

    // Sanitize the file name to remove potentially dangerous characters
    $fileNewName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileNewName);

    // Verify the MIME type of the uploaded file
    $detectedMimeType = mime_content_type($fileTmpName);
    if (!in_array($detectedMimeType, $allowedFileTypes)) {
        echo json_encode(['error' => 'File MIME type does not match the expected type']);
        exit;
    }

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($fileTmpName, $uploadDir . $fileNewName)) {
        // Set proper file permissions (readable by the web server but not writable)
        chmod($uploadDir . $fileNewName, 0644);

        // Respond with the file URL
        echo json_encode([
            'location' => 'assets/uploads/' . $fileNewName
        ]);
    } else {
        echo json_encode(['error' => 'Failed to upload image']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded or an error occurred']);
}
