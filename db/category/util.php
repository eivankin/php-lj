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
}

function delete_category(int $id)
{
}

function get_category(int $id)
{
}
