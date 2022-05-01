<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function &get_connection(): mysqli {
    static $db = null;

    if ($db === null) {
        $db = new mysqli(
            "localhost",
            "root",
            "",
            "blog"
        );
    }

    if ($db->connect_error) {
        die("Не удалось подключиться к базе данных: " . $db->connect_error);
    }

    return $db;
}


