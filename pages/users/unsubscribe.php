<?php
require_once 'db/user/util.php';
require_once 'pages/users/util.php';

if (!isset($id))
    $id = null;

$permission_id = handle_subscription($id, '/unsubscribe');
if (has_user_permission($_SESSION['user_id'], $permission_id)) {
    remove_permission_from_user($_SESSION['user_id'], $permission_id);
    $message = 'Вы успешно отписались от выбранного пользователя';
} else {
    $message = 'Вы не подписаны на выбранного пользователя';
}
require_once 'pages/base.php';
