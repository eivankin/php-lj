<?php
/**
 * Страница списка тегов.
 * Поддерживается сортировка тегов.
 *
 * В таблице тегов указаны название и доступные для пользователя действия над тегом.
 */

require_once 'db/tag/util.php';
require_once 'db/permission/built-in.php';
$title = 'Список тегов';

$is_admin = isset($_SESSION['user_id']) && has_user_permission($_SESSION['user_id'], ADMIN);

$content = "
<form style='width: 300px'>
    <h3>Сортировка тегов</h3>";

$selected = (!empty($_GET['order_by'])) ? ' selected' : '';

$content .= "
    <div>
        <label for='order_by'>Сортировать по столбцу</label>
        <select id='order_by' name='order_by'>
            <option>Выберите столбец</option>
            <option value='name'{$selected}>Название</option>
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

foreach (get_tags($_GET['order_by'], $_GET['order'] ?? true) as $tag) {
    $actions = "<a href='/entries/?tags[]={$tag['id']}'>Просмотреть публикации</a>";

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