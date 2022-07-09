<?php
/**
 * Страница удаления комментария к публикации.
 * Комментарии могут удалять только их авторы, администраторы и модераторы.
 */
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';;
require_once 'db/comment/util.php';

$title = 'Удаление комментария';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['comment_id'])) {
    $comment = get_comment($_POST['comment_id']);
    if (!isset($comment)) {
        not_found();
    }

    if ($_SESSION['user_id'] != $comment['author_id'] && !has_any_permission($_SESSION['user_id'], [ADMIN, MODERATOR])) {
        $message = 'У вас нет прав для удаления этого комментария';
    } else {
        if (delete_comment($comment['id'])) {
            $message = 'Комментарий успешно удалён';
        } else {
            $message = 'Не удалось удалить комментарий';
        }
    }
} else {
    not_found();
}
require_once 'pages/base.php';
