<?php
/**
 * Страница редактирования категории.
 * Категории могут редактировать только администраторы.
 */

require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';
require_once 'pages/util.php';

$title = 'Редактирование категории';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'categories', '/edit');

$category = get_category($id);
if (!isset($category))
    not_found();

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на редактирование категорий';
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    try {
        update_category($id, $_POST['name'], $_POST['parent']);
        $message = 'Категория успешно обновлена';
    } catch (mysqli_sql_exception $exception) {
        $message = 'Не удалось обновить категорию';
    }
} else {

    $content = '
<form method="post" class="fixed-width">
    <div>
        <label for="name">Название</label>
        <input type="text" name="name" id="name" required value="'
        . $category['name'] .
        '">
    </div>
    <div>
        <label for="parent">Родительская категория</label>
        <select name="parent" id="parent">
            <option value="">Выберите категорию</option>
';

    foreach (get_categories() as $cat) {
        $selected = ($category['parent_id'] == $cat['id']) ? ' selected' : '';
        if ($cat['id'] != $category['id'])
            $content .= "<option value='{$cat['id']}'{$selected}>{$cat['name']}</option>";
    }

    $content .= '
        </select>
    </div>
    <button type="submit">Редактировать</button>
</form>';
}

require_once 'pages/base.php';
