<?php
require_once 'db/connection.php';

const BASE_DIR = 'C:\\OpenServer\\domains\\localhost\\';
const UPLOAD_DIR = 'attachments\\';

/**
 * Эта функция создаёт файловое вложение (изображение) публикации в базе данных.
 * Принимает на вход ID публикации, к которому будет привязано вложение и путь к его файлу на сервере.
 *
 * Возвращает успешность создания вложения (true или false).
 */
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

/**
 * Эта функция возвращает список вложений конкретной публикации.
 * Принимает на вход ID публикации.
 */
function get_entry_attachments(int $entry_id): array
{
    $query = get_connection()->prepare('SELECT * FROM entry_image_attachment WHERE entry_id = ?');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция возвращает вложение по её ID.
 *
 * Если вложение не найдено, возвращает null.
 */
function get_attachment(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM entry_image_attachment WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

/**
 * Эта функция удаляет вложение по её ID.
 * Соответствующий публикации файл на сервере тоже удаляется.
 *
 * Возвращает успешность выполнения удаления (true или false).
 */
function delete_attachment(int $id): bool
{
    try {
        delete_attachment_file(get_attachment($id)['url']);
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

/**
 * Вспомогательная функция, удаляющая файл публикации по данному пути.
 */
function delete_attachment_file(string $url)
{
    unlink(BASE_DIR . str_replace('/', '\\', mb_substr($url, 1)));
}