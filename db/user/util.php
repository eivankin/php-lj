<?php
require_once 'db/connection.php';

function create_user(string $email, string $name, string $password): string
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('INSERT INTO user(email, username, password_hash) VALUES (?, ?, ?)');

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query->bind_param('sss', $email, $name, $password_hash);
    try {
        $query->execute();
    } catch (mysqli_sql_exception $exception) {
        return 'Пользователь с таким e-mail или логином уже зарегистрирован';
    }
    get_connection()->commit();
    return 'Аккаунт успешно создан';
}

function authorize(string $username, string $password): int
{
    $query = get_connection()->prepare('SELECT id, password_hash FROM user WHERE username = ?');

    $query->bind_param('s', $username);
    $query->execute();

    $result = $query->get_result();
    $user_info = $result->fetch_assoc();

    if (password_verify($password, $user_info['password_hash'])) {
        update_last_login($user_info['id']);
        return $user_info['id'];
    }

    return -1;
}

function get_user(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM user WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

function update_last_login(int $id)
{
    $query = get_connection()->prepare('UPDATE user SET last_login = NOW() WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();
}

function get_by_email(string $email)
{
    $query = get_connection()->prepare('SELECT id FROM user WHERE email = ?');
    $query->bind_param('s', $email);
    $query->execute();

    return $query->get_result()->fetch_assoc()['id'];
}

function set_password(int $id, string $password) {
    $query = get_connection()->prepare('UPDATE user SET password_hash = ? WHERE id = ?');
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query->bind_param('si', $password_hash, $id);
    $query->execute();
}

function delete_user(int $id): bool {
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM user WHERE id = ?');
    $query->bind_param('i', $id);
    $result = $query->execute();
    get_connection()->commit();
    return $result;
}

function update_user(int $id, string $username, string $email): bool {
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('UPDATE user SET username = ?, email = ? WHERE id = ?');
    $query->bind_param('ssi', $username, $email, $id);
    try {
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

function verify_password(int $id, string $password): bool {
    $query = get_connection()->prepare('SELECT password_hash FROM user WHERE id = ?');

    $query->bind_param('i', $id);
    $query->execute();

    return password_verify($password, $query->get_result()->fetch_assoc()['password_hash']);
}

function handle_subscription($id): int
{
    require_once 'pages/util.php';
    require_once 'db/permission/built-in.php';

    handle_page_with_id($id, 'users', '/subscribe');
    $user = get_user($id);
    if (!isset($user))
        not_found();

    return get_or_create_permission('subscription_' . $id, 'Подписка на пользователя с ID ' . $id);
}


function get_all_users(): array {
    return get_connection()->query('SELECT id, username, last_login FROM user')->fetch_all(MYSQLI_ASSOC);
}