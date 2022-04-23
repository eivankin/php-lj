<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli(
    "localhost",
    "root",
    "",
    "blog"
);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
