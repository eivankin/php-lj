<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';

$title = 'Подписка на пользователя';
if (!isset($id))
    $id = null;

handle_page_with_id($id, 'users', '/subscribe');
$permission_id = get_or_create_permission('subscription_' . $id, 'Подписка на пользователя с ID ' . $id);
if (has_permission($_SESSION['user_id'], $permission_id)) {
    remove_permission_from_user($_SESSION['user_id'], $permission_id);
    $message = 'Вы успешно отписались от выбранного пользователя';
} else {
    $message = 'Вы не подписаны на выбранного пользователя';
}
require_once 'pages/base.php';
