<?php
require_once 'db/connection.php';

function create_tag(string $name)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO tag(name) VALUES (?)');
    $query->bind_param('s', $name);
    $query->execute();
    get_connection()->commit();
}

function update_tag(int $id, string $name)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('UPDATE tag SET name = ? WHERE id = ?');
    $query->bind_param('si', $name, $id);
    $query->execute();
    get_connection()->commit();
}

function add_tag_to_entry(int $tag_id, int $entry_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO entry_to_tag(entry_id, tag_id) VALUES (?, ?)');
    $query->bind_param('ii', $entry_id, $tag_id);
    $query->execute();
    get_connection()->commit();
}

function remove_tag_from_entry(int $tag_id, int $entry_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM entry_to_tag WHERE entry_id = ? AND tag_id = ?');
    $query->bind_param('ii', $entry_id, $tag_id);
    $query->execute();
    get_connection()->commit();
}

function get_tag(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM tag WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

function get_all_tags(): array
{
    return get_connection()->query('SELECT * FROM tag')->fetch_all(MYSQLI_ASSOC);
}

function delete_tag(int $id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('DELETE FROM tag WHERE id = ?');
        $query->bind_param('i', $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}


function get_entry_tags(int $entry_id): array
{
    $query = get_connection()->prepare('SELECT * FROM tag WHERE id IN 
                        (SELECT tag_id FROM entry_to_tag WHERE entry_id = ?)');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}


function has_tag(int $entry_id, int $tag_id): bool {
    $query = get_connection()->prepare('SELECT * FROM entry_to_tag WHERE entry_id = ? AND tag_id = ?');
    $query->bind_param('ii', $entry_id, $tag_id);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['tag_id']);
}