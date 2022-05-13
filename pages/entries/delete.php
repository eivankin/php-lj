<?php
/**
 * Страница удаления публикации.
 * Публикации могут удалять только их авторы, администраторы и модераторы.
 */

require_once 'pages/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/permission/built-in.php';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '/delete');

$entry = get_entry($id);
if (!isset($entry)) {
    not_found();
}

$title = 'Удаление публикации';

if ($_SESSION['user_id'] == $entry['author_id'] || has_any_permission($_SESSION['user_id'], [ADMIN, MODERATOR])) {
    if (delete_entry($id))
        $message = 'Публикация успешно удалена';
    else
        $message = 'Не удалось удалить публикацию';
} else {
    $message = 'У вас нет прав для удаления этой публикации';
}
require_once 'pages/base.php';