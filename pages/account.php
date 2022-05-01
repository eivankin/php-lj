<?php
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';

login_required('/account');

$title = 'Личный кабинет';
$content = json_encode(get_user($_SESSION['user_id']));
$content .= "<a href=\"/users/{$_SESSION['user_id']}/edit\">Редактировать данные профиля</a>" . "<a href=\"/users/{$_SESSION['user_id']}/delete\">Удалить аккаунт</a>";
//if (has_permission($_SESSION['user_id'], ADMIN))
//    $content .= '<a href="/admin/">Панель администратора</a>';

require_once 'pages/base.php';
