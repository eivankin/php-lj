<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';

$title = 'Подписка на пользователя';
if (!isset($id))
    $id = null;

handle_page_with_id($id, 'users', '/subscribe');
$permission_id = get_or_create_permission('subscription_' . $id, 'Подписка на пользователя с ID ' . $id);
if (has_permission($_SESSION['user_id'], $permission_id)) {
    $message = 'Вы уже подписаны на этого пользователя';
} else {
    add_permission_to_user($_SESSION['user_id'], $permission_id);
    $message = 'Вы успешно подписались на выбранного пользователя';
}
require_once 'pages/base.php';
