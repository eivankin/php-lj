<?php
require_once 'db/connection.php';

function create_user(string $email, string $name, string $password): string
{
    $db = get_connection();
    $query = $db->prepare('INSERT INTO user(email, username, password_hash) VALUES (?, ?, ?)');

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query->bind_param('sss', $email, $name, $password_hash);
    try {
        $query->execute();
    } catch (mysqli_sql_exception $exception) {
        return 'Пользователь с таким e-mail или логином уже зарегистрирован';
    }
    $db->close();
    return 'Аккаунт успешно создан';
}

function authorize(string $username, string $password): int
{

    $db = get_connection();
    $query = $db->prepare('SELECT id, password_hash FROM user WHERE username = ?');

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

function get_user(int $id): array
{
    $db = get_connection();
    $query = $db->prepare('SELECT * FROM user WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

function update_last_login(int $id)
{
    $db = get_connection();
    $query = $db->prepare('UPDATE user SET last_login = NOW() WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();
}

function get_by_email(string $email)
{
    $db = get_connection();
    $query = $db->prepare('SELECT id FROM user WHERE email = ?');
    $query->bind_param('s', $email);
    $query->execute();

    return $query->get_result()->fetch_assoc()['id'];
}

function set_password(int $id, string $password) {
    $db = get_connection();
    $query = $db->prepare('UPDATE user SET password_hash = ? WHERE id = ?');
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query->bind_param('si', $password_hash, $id);
    $query->execute();
}