<?php
/**
 * Страница показа публикации.
 * Для просмотра публикации пользователь должен быть её автором или обладать хотя бы одним правом из списка для её просмотра.
 * Если этот список пуст, то публикация доступна всем, в том числе и неаутентифицированным пользователям.
 * Аутентифицированные пользователи увеличивают количество просмотров публикации.
 */

require_once 'pages/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/permission/util.php';
require_once 'db/blog_entry/views.php';
require_once 'db/user/util.php';
require_once 'db/category/util.php';
require_once 'pages/entries/comments/util.php';
require_once 'db/comment/util.php';
require_once 'pages/entries/util.php';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '', false);

$entry = get_entry($id);
if (!isset($entry)) {
    not_found();
}

$title = $entry['title'];

$permissions = array_map(function ($p) {
    return $p['id'];
}, get_entry_permissions($id));

if (count($permissions) > 0 &&
    (!isset($_SESSION['user_id']) || !can_view($_SESSION['user_id'], $permissions, $entry))) {
    $message = 'У вас нет прав для просмотра этой публикации';
} else {
    if (isset($_SESSION['user_id']))
        create_view($_SESSION['user_id'], $id);

    $author = get_user($entry['author_id']);
    $views_count = get_views_count($id);
    $category = get_category($entry['category_id']);
    $tags = join(' | ', array_map(function ($t) {
        return "<a href='/tags/{$t['id']}'>{$t['name']}</a>";
    }, get_entry_tags($entry['id'])));

    $content = "
    <h1>{$entry['title']}</h1>
    <p><b>Автор:</b> <a href='/users/{$entry['author_id']}'>{$author['username']}</a>
    <a href='/users/{$entry['author_id']}/subscribe'><button>Подписаться</button></a></p>
    <p><b>Просмотры:</b> {$views_count}</p>
    <p>{$entry['content']}</p>
    ";

    $attachments = get_entry_attachments($id);
    if (count($attachments) > 0) {
        $content .= '<h2>Прикреплённые изображения</h2><p class="img-gallery">';

        foreach ($attachments as $attachment) {
            $content .= make_image($attachment);
        }

        $content .= '</p>';
    }

    $content .= "
    <p><b>Категория:</b> {$category['name']}</p>
    <p><b>Теги:</b> {$tags}</p>
    <h3>Комментарии к публикации</h3>
    ";

    $comments = get_entry_comments($id);
    if (count($comments) < 1) {
        $content .= '<p>К данной публикации пока что нет комментариев.</p>';
    } else {
        $content .= '<hr>';
        foreach ($comments as $comment) {
            $content .= make_comment_card($comment,
                    has_any_permission($_SESSION['user_id'], [ADMIN, MODERATOR]),
                    $_SESSION['user_id'] == $comment['author_id']) . '<hr>';
        }
    }
    $content .= "
        <h4>Добавить комментарий</h4>
        <form style='width: 300px' method='post' action='./{$id}/comments/new'>
            <div>
                <label for='comment_text'>Текст комментария</label>
                <input type='text' id='comment_text' name='comment_text' required>
            </div>
            <button type='submit'>Добавить</button>
        </form>
    ";

}

require_once 'pages/base.php';
