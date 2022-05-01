<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';

$title = 'Управление правами пользователя';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'users', '/ban');
if (has_permission($_SESSION['user_id'], MODERATOR)) {
    remove_permission_from_user($id, CAN_PUBLISH);
    $message = 'Выбранный пользователь успешно лишён прав на публикацию материалов';
} else {
    $message = 'У вас нет прав для управления разрешениями пользователей';
}
require_once 'pages/base.php';
