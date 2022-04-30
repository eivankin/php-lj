<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

require_once 'db/user/util.php';

$title = 'Личный кабинет';
$content = json_encode(get_user($_SESSION['user_id']));

require_once 'pages/base.php';
