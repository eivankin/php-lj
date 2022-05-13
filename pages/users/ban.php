<?php
/**
 * Страница блокировки (лишения права на публикацию материалов) пользователя.
 * Только модераторы могут пользоваться этой страницей.
 */

require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/user/util.php';

$title = 'Управление правами пользователя';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'users', '/ban');

$user = get_user($id);
if (!isset($user))
    not_found();

if (has_user_permission($_SESSION['user_id'], MODERATOR)) {
    remove_permission_from_user($id, CAN_PUBLISH);
    $message = 'Выбранный пользователь успешно лишён прав на публикацию материалов';
} else {
    $message = 'У вас нет прав для управления разрешениями пользователей';
    http_response_code(403);
}
require_once 'pages/base.php';
