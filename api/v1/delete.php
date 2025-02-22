<?php

// connect the database
require '../../Database.php';

// check for parameters
if (isset($_GET['id']) && isset($_GET['t'])) {
    $id = htmlentities(strip_tags($_GET['id']));
    $table = htmlentities(strip_tags($_GET['t']));

    if ($table === 'admins') {

        // delete data from table
        $database->delete($table, [
            "admin_id" => $id
        ]);
    } else {

        // delete data from table
        $database->delete($table, [
            "id" => $id
        ]);
    }


    // return JSON response with 'ok' status
    echo json_encode(["response" => "ok"]);
} else {
    // return error if parameters are missing
    echo json_encode(["response" => "error", "message" => "Missing parameters"]);
}
