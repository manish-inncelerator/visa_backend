<?php

// connect the database
require '../../Database.php';

// check for parameters
if (isset($_GET['id']) && isset($_GET['t']) && isset($_GET['action'])) {
    $id = htmlentities(strip_tags($_GET['id']));
    $table = htmlentities(strip_tags($_GET['t']));
    $action = htmlentities(strip_tags($_GET['action']));

    if ($action === 'ban') {
        $do = 0;
    } else if ($action === 'unban') {
        $do = 1;
    } else {
        echo 'oops!';
        exit();
    }

    // Update data in the table
    $database->update($table, ['is_active' => $do], [
        'id' => $id
    ]);


    // return JSON response with 'ok' status
    echo json_encode(["response" => "ok"]);
} else {
    // return error if parameters are missing
    echo json_encode(["response" => "error", "message" => "Missing parameters"]);
}
