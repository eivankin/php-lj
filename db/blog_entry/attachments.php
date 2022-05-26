<?php
require_once 'db/connection.php';

const BASE_DIR = 'C:\\OpenServer\\domains\\localhost\\';
const UPLOAD_DIR = 'attachments\\';

function create_attachment(int $entry_id, string $upload_path): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare(
            'INSERT INTO entry_image_attachment(entry_id, url) VALUES (?, ?)');
        $query->bind_param('is', $entry_id, $upload_path);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

function get_entry_attachments(int $entry_id): array
{
    $query = get_connection()->prepare('SELECT * FROM entry_image_attachment WHERE entry_id = ?');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function delete_attachment(int $id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare(
            'DELETE FROM entry_image_attachment WHERE id = ?');
        $query->bind_param('i', $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}