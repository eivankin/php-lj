<?php

require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';

$title = 'Удаление категории';
if (!isset($id))
    $id = null;
handle_page_with_id($id, 'categories', '/delete');

if (!has_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на удаление категорий';
    http_response_code(403);
} else {
    if (delete_category($id)) {
        $message = 'Категория успешно удалена';
    } else {
        $message = 'Не удалось удалить категорию';
    }
}
require_once 'pages/base.php';
