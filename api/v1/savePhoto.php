<?php
// api/v1/savePhoto.php
session_start();

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['message' => 'Invalid request method']);
    exit;
}

// Check if the user is authorized
if (!$_SESSION['admin_id']) {
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id']) || empty($data['country']) || empty($data['filename']) || empty($data['imageUrl'])) {
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}

$id = $data['id'];
$country = $data['country'];
$fileName = $data['filename'];
$imageUrl = $data['imageUrl'];

// Sanitize the country name for use in the folder path
$sanitizedCountry = preg_replace('/[^a-zA-Z0-9_-]/', '_', $country);

// Define the upload directory
$uploadDir = "../../uploads/country/$sanitizedCountry/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
}

// Sanitize the filename
$sanitizedFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);

// Download the image from the URL
$imageData = file_get_contents($imageUrl);
if ($imageData === false) {
    echo json_encode(['message' => 'Failed to download image from URL']);
    exit;
}

// Save the image to the upload directory
$destination = $uploadDir . $sanitizedFileName;
if (file_put_contents($destination, $imageData)) {
    // Save metadata to the database using Medoo
    require '../../Database.php';

    // Check the current count of images for the country
    $imageCount = $database->count('country_images', [
        'country_id' => $id
    ]);

    // Enforce the limit of 5 images per country
    if ($imageCount >= 5) {
        echo json_encode(['message' => 'Maximum limit of 5 images reached for this country', 'redirect' => "view.php?country=$country"]);
        exit;
    }

    // Insert data into the `country_images` table
    $database->insert('country_images', [
        'country_id' => $id,
        'fallback_url' => $imageUrl,
        'photo_path' => 'uploads/country/' . $sanitizedCountry . '/' . $sanitizedFileName,
        'created_at' => date('Y-m-d H:i:s') // Optional: Add a timestamp
    ]);

    // Check if the insert was successful
    if ($database->id()) {
        // Increment the image count
        $imageCount++;

        // Check if the image count has reached 5
        if ($imageCount == 5) {
            echo json_encode(['message' => 'Image and metadata saved successfully. 5 images reached.', 'redirect' => "view.php?country=countries"]);
        } else {
            echo json_encode(['message' => 'Image and metadata saved successfully', 'count' => $imageCount]);
        }
    } else {
        echo json_encode(['message' => 'Failed to save metadata to database']);
    }
} else {
    echo json_encode(['message' => 'Failed to save image']);
}
