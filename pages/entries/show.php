<?php
require_once 'pages/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/permission/util.php';
require_once 'db/blog_entry/views.php';
require_once 'db/user/util.php';
require_once 'db/category/util.php';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '/', false);

$entry = get_entry($id);
if (!isset($entry)) {
    not_found();
}

$title = $entry['title'];

$permissions = array_map(function ($p) {
    return $p['id'];
}, get_entry_permissions($id));

if (count($permissions) > 0 &&
    (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != $entry['author_id'] && !has_any_permission($_SESSION['user_id'], $permissions)))) {
    $message = 'У вас нет прав для просмотра этой публикации';
} else {
    if (isset($_SESSION['user_id']))
        create_view($_SESSION['user_id'], $id);

    $author = get_user($entry['author_id']);
    $views_count = get_views_count($id);
    $category = get_category($entry['category_id']);
    $tags = join(' | ', array_map(function ($t) {
        return $t['name'];
    }, get_entry_tags($entry['id'])));

    $content = "
    <h1>{$entry['title']}</h1>
    <p><b>Автор:</b> <a href='/users/{$entry['author_id']}'>{$author['username']}</a>
    <a href='/users/{$entry['author_id']}/subscribe'><button>Подписаться</button></a></p>
    <p><b>Просмотры:</b> {$views_count}</p>
    <p>{$entry['content']}</p>
    <p><b>Категория:</b> {$category['name']}</p>
    <p><b>Теги:</b> {$tags}</p>
    ";
}

require_once 'pages/base.php';
