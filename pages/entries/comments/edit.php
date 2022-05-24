<?php
require_once 'pages/util.php';
require_once 'db/comment/util.php';

$title = 'Редактирование комментария';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comment_id']) && !empty($_POST['comment_text'])) {
    $comment = get_comment($_POST['comment_id']);
    if (!isset($comment)) {
        not_found();
    }

    if ($_SESSION['user_id'] != $comment['author_id']) {
        $message = 'У вас нет прав для редактирования этого комментария';
    } else {
        if (edit_comment($comment['id'], $_POST['comment_text'])) {
            $message = 'Комментарий успешно изменён';
        } else {
            $message = 'Не удалось изменить комментарий';
        }
    }
} else {
    not_found();
}
require_once 'pages/base.php';
