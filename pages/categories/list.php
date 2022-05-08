<?php
require_once 'db/category/util.php';
require_once 'db/permission/built-in.php';
$title = 'Список категорий';

$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);

$content = "
<form style='width: 300px'>
    <h3>Сортировка категорий</h3>";

$selected = ['', ''];
$selected_index = null;
if ($_GET['order_by'] == 'name') {
    $selected_index = 0;
} elseif ($_GET['order_by'] == 'parent_id') {
    $selected_index = 1;
}

if (isset($selected_index)) {
    $selected[$selected_index] = ' selected';
}

$content .= "
    <div>
        <label for='order_by'>Сортировать по столбцу</label>
        <select id='order_by' name='order_by'>
            <option>Выберите столбец</option>
            <option value='name'{$selected[0]}>Название</option>
            <option value='parent_id'{$selected[1]}>Родительская категория</option>
        </select>
    </div>";

$selected = [' selected', ''];
if (!empty($_GET['order']))
    $selected = array_reverse($selected);
$content .= "
    <div>
        <label for='order'>Тип сортировки</label>
        <select id='order' name='order'>
            <option value='0'{$selected[0]}>По возрастанию</option>
            <option value='1'{$selected[1]}>По убыванию</option>
        </select>
    </div>
    <button type='submit'>Сортировать</button>
    <a href='./'><button type='button'>Сбросить фильтры</button></a>
</form>
";

if ($is_admin)
    $content .= '<p><a href="./new"><button>Добавить категорию</button></a></p>';

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


foreach (get_all_categories($_GET['order_by'], $_GET['order'] ?? true) as $category) {
    $actions = "<a href='/entries/?category={$category['id']}'>Просмотреть публикации</a>";

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
