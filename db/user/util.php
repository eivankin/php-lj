<?php
require_once 'db/connection.php';
require_once 'db/permission/util.php';

const CREATION_SUCCESS_MSG = 'Аккаунт успешно создан';
/**
 * Эта функция создаёт нового пользователя в базе данных.
 * Возвращает сообщение об успешности операции.
 *
 * Пароль пользователя сохраняется в хэшированном виде.
 */
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
    return CREATION_SUCCESS_MSG;
}

/**
 * Эта функция проверяет, что существует пользователь с переданным логином и корректность введённого пароля.
 * В случае успеха обновляет дату последнего входа пользователя и возвращает ID пользователя, иначе -1.
 */
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

/**
 * Эта функция возвращает пользователя по его ID.
 */
function get_user(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM user WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

/**
 * Эта функция обновляет дату последнего входа пользователя на текущее время.
 */
function update_last_login(int $id)
{
    $query = get_connection()->prepare('UPDATE user SET last_login = NOW() WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();
}

/**
 * Эта функция возвращает пользователя по его e-mail.
 */
function get_user_by_email(string $email)
{
    $query = get_connection()->prepare('SELECT id FROM user WHERE email = ?');
    $query->bind_param('s', $email);
    $query->execute();

    return $query->get_result()->fetch_assoc()['id'];
}

/**
 * Эта функция обновляет пароль пользователя на переданный в качестве параметра.
 */
function set_password(int $id, string $password) {
    $query = get_connection()->prepare('UPDATE user SET password_hash = ? WHERE id = ?');
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query->bind_param('si', $password_hash, $id);
    $query->execute();
}

/**
 * Эта функция удаляет пользователя по его ID.
 *
 * Материалы, опубликованные пользователем, удалятся автоматически на стороне базы данных.
 */
function delete_user(int $id): bool {
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('DELETE FROM user WHERE id = ?');
    $query->bind_param('i', $id);
    $result = $query->execute();
    get_connection()->commit();
    return $result;
}

/**
 * Эта функция обновляет атрибуты существующего пользователя.
 */
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

/**
 * Эта функция проверяет корректность пароля пользователя по его ID.
 */
function verify_password(int $id, string $password): bool {
    $query = get_connection()->prepare('SELECT password_hash FROM user WHERE id = ?');

    $query->bind_param('i', $id);
    $query->execute();

    return password_verify($password, $query->get_result()->fetch_assoc()['password_hash']);
}

/**
 * Эта функция возвращает список всех существующих пользователей.
 */
function get_all_users(): array {
    return get_connection()->query('SELECT id, username, last_login FROM user')->fetch_all(MYSQLI_ASSOC);
}