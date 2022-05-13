<?php
/**
 * Страница удаления пользователя.
 * Только администраторы могут удалять пользователей.
 * Администратор не может удалить свой собственный аккаунт.
 */

require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/user/util.php';

$title = 'Удаление пользователя';
if (!isset($id))
    $id = null;
handle_page_with_id($id, 'users', '/delete');

if ($_SESSION['user_id'] == $id || !has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на удаление этого пользователя';
    http_response_code(403);
} else {
    if (delete_user($id)) {
        $message = 'Пользователь успешно удалён';
    } else {
        $message = 'Не удалось удалить пользователя';
    }
}
require_once 'pages/base.php';
