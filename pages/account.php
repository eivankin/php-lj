<?php
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';
require_once 'pages/users/util.php';

login_required('/account');

$title = 'Личный кабинет';

$content = "<a href=\"/users/{$_SESSION['user_id']}/edit\"><button>Редактировать данные профиля</button></a>";
$content .= get_info($_SESSION['user_id']);

require_once 'pages/base.php';
