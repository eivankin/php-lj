<?php
/**
 * Страница создания категорий.
 * Только администраторы могут создавать категории.
 */

require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';

$title = 'Добавление категории';

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на создание категорий';
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    try {
        create_category($_POST['name'], $_POST['parent']);
        $message = 'Категория успешно добавлена';
    } catch (mysqli_sql_exception $exception) {
        $message = 'Не удалось создать категорию';
    }
}

$content = '
<form method="post" class="fixed-width" style="width: 300px">
    <div>
        <label for="name">Название</label>
        <input type="text" name="name" id="name" required>
    </div>
    <div>
        <label for="parent">Родительская категория</label>
        <select name="parent" id="parent">
            <option value="">Выберите категорию</option>
';

foreach (get_categories() as $category) {
    $content .= "<option value='{$category['id']}'>{$category['name']}</option>";
}

$content .= '
        </select>
    </div>
    <button type="submit">Создать</button>
</form>';

require_once 'pages/base.php';
