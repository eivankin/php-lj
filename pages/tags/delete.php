<?php

require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/tag/util.php';

$title = 'Удаление тега';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'tags', '/delete');

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на удаление тегов';
    http_response_code(403);
} else {
    if (delete_tag($id)) {
        $message = 'Тег успешно удалён';
    } else {
        $message = 'Не удалось удалить тег';
    }
}
require_once 'pages/base.php';
