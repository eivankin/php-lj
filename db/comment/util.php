<?php
require_once 'db/connection.php';

/**
 * Эта функция создаёт комментарий к публикации в базе данных.
 * Принимает на вход текст комментария, ID автора и публикации, к которым будет привязан комментарий.
 *
 * Возвращает успешность создания вложения (true или false).
 */
function create_comment(int $user_id, int $entry_id, string $comment_text): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare(
            'INSERT INTO entry_comment(author_id, entry_id, text, edited, published) VALUES (?, ?, ?, NOW(), NOW())');
        $query->bind_param('iis', $user_id, $entry_id, $comment_text);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 * Эта функция удаляет комментарий по его ID.
 *
 * Возвращает успешность выполнения удаления (true или false).
 */
function delete_comment(int $id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('DELETE FROM entry_comment WHERE id = ?');
        $query->bind_param('i', $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 * Эта функция возвращает комментарий по его ID.
 *
 * Если комментарий не найден, возвращает null.
 */
function get_comment(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM entry_comment WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

/**
 * Эта функция возвращает список комментариев конкретной публикации.
 * Принимает на вход ID публикации.
 */
function get_entry_comments(int $entry_id): array
{
    $query = get_connection()->prepare('SELECT * FROM entry_comment WHERE entry_id = ? ORDER BY published');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция возвращает количество комментариев конкретной публикации.
 * Принимает на вход ID публикации.
 */
function get_entry_comment_count(int $entry_id): int
{
    $query = get_connection()->prepare('SELECT COUNT(*) AS comment_count FROM entry_comment WHERE entry_id = ?');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_assoc()['comment_count'];
}

/**
 * Эта функция изменяет текст комментария и автоматически обновляет дату его редактирования.
 * Принимает на вход ID комментария и его новый текст.
 */
function edit_comment(int $id, string $comment_text): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('UPDATE entry_comment SET text = ?, edited = NOW() WHERE id = ?');
        $query->bind_param('si', $comment_text, $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}