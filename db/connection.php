<?php
// Заставляем базу данных не молчать об ошибках и вызывать исключения в случае ошибки.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Эта функция создаёт подключение к базе данных при первом вызове и
 * передаёт ссылку на существующее подключение при всех последующих вызовах.
 */
function &get_connection(): mysqli
{
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


