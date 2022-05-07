<?php

require_once 'db/blog_entry/util.php';

function create_view(int $user_id, int $entry_id)
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('INSERT INTO entry_view(user_id, entry_id, date) VALUES (?, ?, NOW())');
        $query->bind_param('ii', $user_id, $entry_id);
        $query->execute();
        get_connection()->commit();
    } catch (mysqli_sql_exception $exception) {
        // do nothing
    }
}

function get_views_count(int $entry_id): int {
    $query = get_connection()->prepare('SELECT COUNT(*) as count FROM entry_view WHERE entry_id = ?');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_assoc()['count'];
}
