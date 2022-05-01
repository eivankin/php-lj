<?php
require_once 'db/connection.php';

function get_or_create_permission(string $internal_name, string $description): int
{
    $db = get_connection();
    $query = $db->prepare('INSERT INTO permission(internal_name, description) VALUES (?, ?)');
    $query->bind_param('ss', $internal_name, $description);
    try {
        $query->execute();
        $db->close();
        return $query->insert_id;
    } catch (mysqli_sql_exception $exception) {
        $query = $db->prepare('SELECT id FROM permission WHERE internal_name = ?');
        $query->bind_param('s', $internal_name);
        $query->execute();

        return $query->get_result()->fetch_assoc()['id'];
    }
}

function get_permission(int $id): array
{
    $db = get_connection();
    $query = $db->prepare('SELECT * FROM permission WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

function has_permission(int $user_id, int $permission_id): bool
{
    $db = get_connection();
    $query = $db->prepare('SELECT * FROM user_to_permission WHERE user_id = ? AND permission_id = ?');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['user_id']);
}

function add_permission_to_user(int $user_id, int $permission_id) {
    $db = get_connection();
    $query = $db->prepare('INSERT INTO user_to_permission(user_id, permission_id) VALUES (?, ?)');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    $db->close();
}

//function has_any_permission(int $user_id, array $permission_ids): bool
//{
//
//}