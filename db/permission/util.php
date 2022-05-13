<?php
require_once 'db/connection.php';

const SUBSCRIPTION_PREFIX = 'subscription_';

/**
 * Эта функция пытается найти существующее разрешение по его уникальному имени, иначе создаёт его.
 *
 * Возвращает ID найденного или созданного разрешения.
 */
function get_or_create_permission(string $internal_name, string $description): int
{
    $existing_permission = get_permission_by_name($internal_name);
    if (!isset($existing_permission['id'])) {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('INSERT INTO permission(internal_name, description) VALUES (?, ?)');
        $query->bind_param('ss', $internal_name, $description);
        $query->execute();
        get_connection()->commit();
        return $query->insert_id;
    }
    return $existing_permission['id'];
}

/**
 * Эта функция возвращает разрешение по его внутреннему уникальному имени.
 *
 * Возвращает null если разрешения не существует.
 */
function get_permission_by_name(string $internal_name)
{
    $query = get_connection()->prepare('SELECT id FROM permission WHERE internal_name = ?');
    $query->bind_param('s', $internal_name);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

/**
 * Эта функция проверяет наличие права у пользователя.
 * Принимает на вход ID пользователя и разрешения.
 */
function has_user_permission(int $user_id, int $permission_id): bool
{
    $query = get_connection()->prepare('SELECT * FROM user_to_permission WHERE user_id = ? AND permission_id = ?');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['user_id']);
}

/**
 * Эта функция добавляет разрешение пользователю.
 * Принимает на вход ID пользователя и разрешения.
 */
function add_permission_to_user(int $user_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO user_to_permission(user_id, permission_id) VALUES (?, ?)');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция забирает разрешение у пользователя.
 * Принимает на вход ID пользователя и разрешения.
 */
function remove_permission_from_user(int $user_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM user_to_permission WHERE user_id = ? AND permission_id = ?');
    $query->bind_param('ii', $user_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция проверяет наличие хотя бы одного разрешения из списка у пользователя.
 * Принимает на вход ID пользователя и список ID разрешений.
 */
function has_any_permission(int $user_id, array $permission_ids): bool
{
    $values = str_repeat('?,', count($permission_ids) - 1) . '?';
    $query = get_connection()->prepare("SELECT * FROM user_to_permission WHERE user_id = ? AND permission_id IN ({$values})");
    $query->bind_param('i' . str_repeat('i', count($permission_ids)), $user_id, ...$permission_ids);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['user_id']);
}

/**
 * Эта функция возвращает список разрешений конкретного пользователя.
 */
function get_user_permissions(int $user_id): array
{
    $query = get_connection()->prepare('SELECT * FROM permission WHERE id IN 
                               (SELECT permission_id FROM user_to_permission WHERE user_id = ?)');
    $query->bind_param('i', $user_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция возвращает список всех существующих разрешений.
 */
function get_all_permissions(): array
{
    return get_connection()->query('SELECT * FROM permission')->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция возвращает число подписчиков конкретного пользователя.
 * Так как подписки реализованы через разрешения, то эта функция находится в том же файле,
 * что и другие функции по работе с разрешениями.
 */
function get_subscribers_count(int $user_id): int
{
    $query = get_connection()->prepare('SELECT COUNT(*) as count FROM user_to_permission WHERE permission_id = ?');
    $query->bind_param('i',
        get_connection()->query("SELECT id FROM permission WHERE internal_name = 'subscription_{$user_id}'")
            ->fetch_assoc()['id']);
    $query->execute();
    return $query->get_result()->fetch_assoc()['count'];
}

/**
 * Эта функция добавляет право к списку необходимых для просмотра публикации.
 * Принимает на вход ID разрешения и публикации.
 */
function add_permission_to_entry(int $entry_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO entry_to_permission(entry_id, permission_id) VALUES (?, ?)');
    $query->bind_param('ii', $entry_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция удаляет право из списка необходимых для просмотра публикации.
 * Принимает на вход ID разрешения и публикации.
 */
function remove_permission_from_entry(int $entry_id, int $permission_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM entry_to_permission WHERE entry_id = ? AND permission_id = ?');
    $query->bind_param('ii', $entry_id, $permission_id);
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция получает список прав, необходимых для просмотра публикации.
 * Принимает на вход ID публикации.
 */
function get_entry_permissions(int $entry_id): array
{
    $query = get_connection()->prepare('SELECT * FROM permission WHERE id IN 
                        (SELECT permission_id FROM entry_to_permission WHERE entry_id = ?)');
    $query->bind_param('i', $entry_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция получает список прав, необходимых для просмотра публикации.
 * Принимает на вход ID публикации.
 */
function has_entry_permission(int $entry_id, int $permission_id): bool
{
    $query = get_connection()->prepare('SELECT * FROM entry_to_permission WHERE entry_id = ? AND permission_id = ?');
    $query->bind_param('ii', $entry_id, $permission_id);
    $query->execute();
    return isset($query->get_result()->fetch_assoc()['entry_id']);
}

/**
 * Эта функция возвращает ID разрешения, соответствующего подписке на конкретного пользователя.
 * Принимает на вход ID пользователя.
 */
function get_subscription_id(int $user_id): int
{
    return get_or_create_permission(SUBSCRIPTION_PREFIX . $user_id,
        'Подписка на пользователя с ID ' . $user_id);
}

/**
 * Эта функция возвращает список ID пользователей, на которых подписан конкретный пользователь.
 * Принимает на вход ID пользователя.
 */
function get_subscriptions(int $user_id): array
{
    $prefix = SUBSCRIPTION_PREFIX;
    $query = get_connection()->prepare("SELECT CAST(REPLACE(internal_name, '{$prefix}', '') AS UNSIGNED) AS user_id 
        FROM permission WHERE internal_name LIKE '{$prefix}%' AND id IN 
                                                              (SELECT permission_id FROM user_to_permission WHERE user_id = ?)");
    $query->bind_param('i', $user_id);
    $query->execute();
    return array_map(function ($a) {
        return $a[0];
    }, $query->get_result()->fetch_all());
}