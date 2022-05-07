<?php
require_once 'db/blog_entry/util.php';
require_once 'db/user/util.php';
require_once 'db/category/util.php';
require_once 'db/blog_entry/views.php';

$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);
$is_moderator = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], MODERATOR);


$title = 'Публикации';
$content = '
<p><a href="./new"><button>Добавить публикацию</button></a></p>
';
$content .= '<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Заголовок</th>
            <th>Автор</th>
            <th>Категория</th>
            <th>Дата публикации</th>
            <th>Дата последнего редактирования</th>
            <th>Теги</th>
            <th>Просмотры</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>';

foreach (get_all_entries() as $entry) {
    $actions = '';
    if ($is_admin || $entry['author_id'] == $_SESSION['user_id']) {
        $actions .= "<a href='./{$entry['id']}/edit'>Редактировать</a> | " .
            "<a href='./{$entry['id']}/delete'>Удалить</a>";
    } elseif ($is_moderator) {
        $actions .= "<a href='./{$entry['id']}/delete'>Удалить</a>";
    }

    $tags = join(' | ', array_map(function ($t) {
        return $t['name'];
    }, get_entry_tags($entry['id'])));

    $views_count = get_views_count($entry['id']);

    $author = get_user($entry['author_id']);
    $category = get_category($entry['category_id']);
    $content .= "<tr>
        <td>{$entry['id']}</td>
        <td><a href='./{$entry['id']}'>{$entry['title']}</a></td>
        <td><a href='/users/{$entry['author_id']}'>{$author['username']}</a></td>
        <td>{$category['name']}</td>
        <td>{$entry['published']}</td>
        <td>{$entry['edited']}</td>
        <td>{$tags}</td>
        <td>{$views_count}</td>
        <td>{$actions}</td>
    </tr>";
}
$content .= '</tbody></table>';
require_once 'pages/base.php';