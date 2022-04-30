<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function get_connection(): mysqli {
    $db = new mysqli(
        "localhost",
        "root",
        "",
        "blog"
    );

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    return $db;
}


