<?php
require_once 'db/connection.php';

function get_or_create_permission(string $internal_name, string $description): int
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO permission(internal_name, description) VALUES (?, ?)');
    $query->bind_param('ss', $internal_name, $description);
    try {
        $query->execute();
        get_connection()->commit();
        return $query->insert_id;
    } catch (mysqli_sql_exception $exception) {
        get_connection()->commit();
        return get_permission_by_name($internal_name)['id'];
    }
}

function get_permission_by_name(string $internal_name): array
{
    $query = get_connection()->prepare('SELECT id FROM permission WHERE internal_name = ?');
    $query->bind_param('s', $internal_name);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

function get_permission(int $id): array
{
    $query = get_connection()->prepare('SELECT * FROM permission WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

function has_permission(int $user_id, int $permission_id): bool
{
    $query = get_connection()->prepare('SELECT * FROM user_to_permission WHERE user_id = ? AND permission_id = ?');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['user_id']);
}

function add_permission_to_user(int $user_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO user_to_permission(user_id, permission_id) VALUES (?, ?)');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

function remove_permission_from_user(int $user_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM user_to_permission WHERE user_id = ? AND permission_id = ?');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

function has_any_permission(int $user_id, array $permission_ids): bool
{
    $values = str_repeat('?,', count($permission_ids) - 1) . '?';
    $query = get_connection()->prepare("SELECT * FROM user_to_permission WHERE user_id = ? AND permission_id IN ({$values})");
    $query->bind_param('i' . str_repeat('i', count($permission_ids)), $user_id, ...$permission_ids);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['user_id']);
}

function get_user_permissions(int $user_id): array
{
    $query = get_connection()->prepare('SELECT * FROM permission WHERE id IN 
                               (SELECT permission_id FROM user_to_permission WHERE user_id = ?)');
    $query->bind_param('i', $user_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_all_permissions(): array
{
    return get_connection()->query('SELECT * FROM permission')->fetch_all(MYSQLI_ASSOC);
}

function get_subscribers_count(int $user_id): int
{
    $query = get_connection()->prepare('SELECT COUNT(*) as count FROM user_to_permission WHERE permission_id = ?');
    $query->bind_param('i',
        get_connection()->query("SELECT id FROM permission WHERE internal_name = 'subscription_{$user_id}'")
            ->fetch_assoc()['id']);
    $query->execute();
    return $query->get_result()->fetch_assoc()['count'];
}

function add_permission_to_entry(int $entry_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO entry_to_permission(entry_id, permission_id) VALUES (?, ?)');
    $query->bind_param('ii', $entry_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

function remove_permission_from_entry(int $entry_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM entry_to_permission WHERE entry_id = ? AND permission_id = ?');
    $query->bind_param('ii', $entry_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

function get_entry_permissions(int $entry_id) {
    $query = get_connection()->prepare('SELECT * FROM permission WHERE id IN 
                        (SELECT permission_id FROM entry_to_permission WHERE entry_id = ?)');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}