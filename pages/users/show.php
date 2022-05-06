<?php
require_once 'pages/users/util.php';

$title = 'Информация о пользователе';

if (!isset($id))
    $id = null;
handle_page_with_id($id, 'users', '/');

if ($id == $_SESSION['user_id']) {
    header('Location: /account');
}

$content = get_info($id);

require_once 'pages/base.php';