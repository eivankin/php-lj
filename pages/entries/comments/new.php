<?php
require_once 'pages/util.php';
require_once 'pages/entries/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/blog_entry/util.php';
require_once 'db/comment/util.php';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '/comments/new');

$title = 'Создание комментария';

if (!has_user_permission($_SESSION['user_id'], CAN_COMMENT) || !can_view($_SESSION['user_id'], array_map(function ($p) {
        return $p['id'];
    }, get_entry_permissions($id)), get_entry($id))) {
    $message = 'У вас нет прав для комментирования этой публикации';
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
    if (create_comment($_SESSION['user_id'], $id, $_POST['comment_text']))
        $message = 'Комментарий успешно создан';
    else
        $message = 'Не удалось создать комментарий';
}

require_once 'pages/base.php';
