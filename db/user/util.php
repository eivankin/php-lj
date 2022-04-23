<?php

function create_user(string $email, string $name, string $password): string
{
    require 'db/connection.php';
    if (isset($db)) {
        $query = $db->prepare('INSERT INTO user(email, username, password_hash) VALUES (?, ?, ?)');

        $query->bind_param("sss", $email, $name, password_hash($password, PASSWORD_DEFAULT));
        try {
            $query->execute();
        } catch (mysqli_sql_exception $exception) {
            return "Пользователь с таким e-mail или логином уже зарегистрирован";
        }
        $db->close();
        return "Аккаунт успешно создан";
    }
    return "Нет подключения к базе данных";
}

function verify(string $username, string $password): bool {
    require 'db/connection.php';
    if (isset($db)) {
        $query = $db->prepare('SELECT password_hash FROM user WHERE username = ?');

        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();

        return password_verify($password, $result->fetch_assoc()['password_hash']);
    }
    else
        return false;
}
