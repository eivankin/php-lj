<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/user/util.php';

$title = 'Удаление пользователя';
if (!isset($id))
    $id = null;
handle_page_with_id($id, 'users', '/delete');
$is_the_same_user = $_SESSION['user_id'] == $id;

if (!$is_the_same_user && !has_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на удаление этого пользователя';
} else {
    if (delete_user($id)) {
        if ($is_the_same_user) {
            header('Location: /logout');
            exit();
        }
        $message = 'Пользователь успешно удалён';
    } else {
        $message = 'Не удалось удалить пользователя';
    }
}
require_once 'pages/base.php';
