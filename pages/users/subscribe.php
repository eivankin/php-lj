<?php
require_once 'db/user/util.php';

if (!isset($id))
    $id = null;

$title = 'Подписка на пользователя';
$permission_id = handle_subscription($id);
if ($_SESSION['user_id'] == $id) {
    $message = 'Нельзя подписаться на самого себя';
} elseif (has_user_permission($_SESSION['user_id'], $permission_id)) {
    $message = 'Вы уже подписаны на этого пользователя';
} else {
    add_permission_to_user($_SESSION['user_id'], $permission_id);
    $message = 'Вы успешно подписались на выбранного пользователя';
}
require_once 'pages/base.php';
