<?php
// Include Medoo for database connection
require '../../Database.php';

$data = '';

// Function to check if country exists
function checkCountryExists($database, $countryName)
{
    return $database->get(
        'countries',
        ['id', 'country_name'],
        ['country_name' => $countryName]
    );
}

// Function to validate and format required documents
function validateRequiredDocuments($requiredDocuments)
{
    if (!is_array($requiredDocuments)) {
        throw new Exception("Required documents must be an array.");
    }

    // Ensure all documents are non-empty strings
    foreach ($requiredDocuments as $document) {
        if (!is_string($document) || empty(trim($document))) {
            throw new Exception("Invalid document name. All documents must be non-empty strings.");
        }
    }

    return $requiredDocuments;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if we have a multipart form data
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        $isMultipart = strpos($contentType, "multipart/form-data") !== false;

        if ($isMultipart) {
            // Handle multipart form data
            $data = $_POST;
        } else {
            // Handle JSON data
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true);
        }

        // Check if JSON is valid (for non-multipart requests)
        if (!$isMultipart && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON data received.");
        }

        // Validate required adminID
        if (empty($data['adminID'])) {
            throw new Exception("Admin ID is required.");
        }

        // Validate required country name
        if (empty($data['countryName'])) {
            throw new Exception("Country name is required.");
        }

        // Check if country already exists
        $existingCountry = checkCountryExists($database, $data['countryName']);
        if ($existingCountry) {
            http_response_code(409); // Conflict status code
            echo json_encode([
                "success" => false,
                "error" => "Country already exists",
                "existingCountry" => $existingCountry
            ]);
            exit;
        }

        // Validate and format required documents
        $requiredDocuments = [];
        if (!empty($data['requiredDocuments'])) {
            $requiredDocuments = validateRequiredDocuments($data['requiredDocuments']);
        }

        // Prepare country data with default values
        $countryData = [
            'country_name' => $data['countryName'],
            'famous_monument' => empty($data['famousMonument']) ? 'Not provided' : $data['famousMonument'],
            'serviceability' => $data['serviceability'] ?? 'Not provided',
            'visa_type' => $data['visaType'] ?? 'Not provided',
            'visa_kind' => $data['visaKind'] ?? 'Not provided',
            'visa_category' => $data['visaCategory'] ?? 'Not provided',
            'visa_entry' => $data['visaEntry'] ?? 'Not provided',
            'visa_validity_unit' => $data['validityUnit'] ?? 'Not provided',
            'stay_duration' => $data['stayDuration'] ?? 'Not provided',
            'visa_validity' => $data['visaValidity'] ?? 'Not provided',
            'visa_department' => $data['visaDepartment'] ?? 'Not provided',
            'processing_time_value' => $data['processingTimeValue'] ?? 'Not provided',
            'processing_time_unit' => $data['processingTimeUnit'] ?? 'Not provided',
            'approval_rate' => $data['approvalRate'] ?? 'Not provided',
            'admin_id' => $data['adminID'],
            'is_active' => 1,
            'required_documents' => json_encode($requiredDocuments), // Save as JSON string
        ];

        // Skip pricing fields if serviceability is "not easy"
        if ($countryData['serviceability'] !== 'not easy') {
            $countryData['portify_fees'] = (float)($data['portifyFees'] ?? 0); // Convert to float
            $countryData['vfs_service_fees'] = (float)($data['VFSService'] ?? 0); // Convert to float
            $countryData['embassy_fee'] = (float)($data['onlyEmbassyFee'] ?? 0); // Convert to float
        }

        // Start transaction
        $database->action(function () use ($database, $countryData) {
            global $data;
            // Insert country details
            $database->insert('countries', $countryData);

            // Check if the insert was successful
            $lastInsertId = $database->id();
            if (!$lastInsertId) {
                throw new Exception("Failed to insert country data.");
            }

            // Return success response with the newly inserted ID
            http_response_code(201); // Created
            echo json_encode([
                "success" => true,
                "message" => "Country details added successfully.",
                "countryId" => $lastInsertId,
                "countryName" => $data['countryName']
            ]);
        });
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Invalid request method. Only POST requests are allowed."
    ]);
}
