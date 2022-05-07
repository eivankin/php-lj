<?php
require_once 'db/tag/util.php';
require_once 'db/permission/built-in.php';
$title = 'Список тегов';

$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);

$content = '';
if ($is_admin)
    $content .= '<p><a href="./new"><button>Добавить тег</button></a></p>';

$content .= '
<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Тег</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>';

foreach (get_all_tags() as $tag) {
    $actions = "<a href='./{$tag['id']}'>Просмотреть публикации</a>";

    if ($is_admin) {
        $actions .= " | <a href='./{$tag['id']}/edit'>Редактировать</a>" .
            " | <a href='./{$tag['id']}/delete'>Удалить</a>";
    }

    $content .= "<tr id='{$tag['id']}'>
    <td>{$tag['id']}</td>
    <td>{$tag['name']}</td>
    <td>{$actions}</td>
</tr>";
}

$content .= '</tbody></table>';

require_once 'pages/base.php';