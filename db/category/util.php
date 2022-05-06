<?php

require_once 'db/connection.php';

function get_all_categories(): array
{
    return get_connection()->query('SELECT * FROM category')->fetch_all(MYSQLI_ASSOC);
}


function create_category(string $name, $parent_id)
{
    get_connection()->begin_transaction();
    if (isset($parent_id) && $parent_id != 0) {
        $query = get_connection()->prepare('INSERT INTO category(name, parent_id) VALUES (?, ?)');
        $query->bind_param('si', $name, $parent_id);
    } else {
        $query = get_connection()->prepare('INSERT INTO category(name, parent_id) VALUES (?, NULL)');
        $query->bind_param('s', $name);
    }
    $query->execute();
    get_connection()->commit();
}

function update_category(int $id, string $name, int $parent_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('UPDATE category SET name = ?, parent_id = ? WHERE id = ?');
    $query->bind_param('ssi', $name, $parent_id, $id);
    $query->execute();
    get_connection()->commit();
}

function delete_category(int $id): bool
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM category WHERE id = ?');
    $query->bind_param('i', $id);
    $result = $query->execute();
    get_connection()->commit();
    return $result;
}

function get_category(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM category WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}
