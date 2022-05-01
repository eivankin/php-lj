<?php
login_required('/account');

require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';

$title = 'Личный кабинет';
$content = json_encode(get_user($_SESSION['user_id']));
//if (has_permission($_SESSION['user_id'], ADMIN))
//    $content .= '<a href="/admin/">Панель администратора</a>';

require_once 'pages/base.php';
