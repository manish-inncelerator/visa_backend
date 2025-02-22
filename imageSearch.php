<?php
// imageSearch.php

// Define API keys
define('PEXELS_API_KEY', 'b40z40lmZhH3kdkHhEUxPBDbovSy4DNRNGQ1dlMVZfBZdSrifuOOQI6C');
define('PIXABAY_API_KEY', '48380235-38d1e009b86afeb5c4a150ec9');

// Allow cross-origin requests (if needed)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Function to search Pexels
function searchPexels($query, $per_page = 10, $page = 1)
{
    $url = "https://api.pexels.com/v1/search?query=" . urlencode($query) . "&per_page=" . $per_page . "&page=" . $page;
    $options = [
        "http" => [
            "header" => "Authorization: " . PEXELS_API_KEY
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}

// Function to search Pixabay
function searchPixabay($query, $per_page = 10, $page = 1)
{
    $url = "https://pixabay.com/api/?key=" . PIXABAY_API_KEY . "&q=" . urlencode($query) . "&per_page=" . $per_page . "&page=" . $page;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Fetch results from both APIs
    $pexelsResults = searchPexels($query, $per_page, $page);
    $pixabayResults = searchPixabay($query, $per_page, $page);

    // Combine results
    $combinedResults = [
        'pexels' => $pexelsResults['photos'] ?? [],
        'pixabay' => $pixabayResults['hits'] ?? []
    ];

    // Return JSON response
    echo json_encode($combinedResults);
} else {
    // Handle invalid requests
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Please provide a search query.']);
}
