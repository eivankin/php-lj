<?php
require_once 'db/category/util.php';
require_once 'db/permission/built-in.php';
$title = 'Список категорий';

$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);

$content = '';
if ($is_admin)
    $content .= '<a href="./new"><button>Добавить категорию</button></a>';

$content .= '
<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>Родительская категория</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>';


foreach (get_all_categories() as $category) {
    $actions = "<a href='./{$category['id']}'>Просмотреть публикации</a>";

    if ($is_admin) {
        $actions .= " | <a href='./{$category['id']}/edit'>Редактировать</a>" .
            " | <a href='./{$category['id']}/delete'>Удалить</a>";
    }

    $parent = 'Нет';
    if (isset($category['parent_id']))
        $parent = "<a href='#{$category['parent_id']}'>№ {$category['parent_id']}</a>";

    $content .= "<tr id='{$category['id']}'>
    <td>{$category['id']}</td>
    <td>{$category['name']}</td>
    <td>{$parent}</td>
    <td>{$actions}</td>
</tr>";
}

$content .= '</tbody></table>';

require_once 'pages/base.php';
