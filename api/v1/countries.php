<?php
session_start();
require '../../Database.php';

// Helper function to send JSON responses
function sendJsonResponse($status, $message, $data = null, $httpCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($httpCode);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}


// Check authorization
if (!isset($_GET['aid']) || $_GET['aid'] != $_SESSION['admin_id']) {
    sendJsonResponse('error', 'Unauthorized access', null, 401);
}


try {
    // Fetch data with joins
    $data = $database->select('countries', [
        '[>]visa_types' => ['visa_type' => 'id'],
        '[>]visa_kinds' => ['visa_kind' => 'id'],
        '[>]visa_categories' => ['visa_category' => 'id'],
        '[>]required_documents' => ['required_documents' => 'id'] // Verify foreign key name
    ], [
        // Explicitly alias columns to avoid conflicts
        'countries.id',
        'countries.country_name',
        'countries.serviceability',
        'countries.visa_type',
        'countries.visa_kind',
        'countries.visa_category',
        'countries.required_documents',
        'countries.stay_duration',
        'countries.visa_validity',
        'countries.visa_validity_unit',
        'countries.visa_entry',
        'countries.visa_department',
        'countries.processing_time_value',
        'countries.processing_time_unit',
        'countries.approval_rate',
        'countries.portify_fees',
        'countries.embassy_fee',
        'countries.vfs_service_fees',
        'countries.admin_id',
        'countries.created_at',
        'countries.edited_at',
        'countries.is_active',
        'visa_types.visa_type(visa_type_name)',
        'visa_kinds.visa_kind(visa_kind_name)',
        'visa_categories.visa_category(visa_category_name)',
        'required_documents.required_document_name'
    ], [
        "ORDER" => ["countries.id" => "DESC"]
    ]);

    if (!empty($data)) {
        sendJsonResponse('success', 'Data retrieved successfully', $data);
    } else {
        sendJsonResponse('error', 'No data found', null);
    }
} catch (Exception $e) {
    sendJsonResponse('error', 'Database error: ' . $e->getMessage(), null, 500);
}
