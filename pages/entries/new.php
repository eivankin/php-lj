<?php
require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';
require_once 'db/blog_entry/util.php';

login_required('/entries/new');

$title = 'Добавить публикацию';
if (has_permission($_SESSION['user_id'], CAN_PUBLISH)) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) &&
        isset($_POST['content']) && isset($_POST['category'])) {
        try {
            create_entry($_POST['title'], $_POST['content'], $_POST['category'],
                $_POST['tag'], $_POST['permission']);
            $message = 'Успешно опубликовано';
        } catch (mysqli_sql_exception $exception) {
            $message = 'Не удалось опубликовать материалы';
        }
    } else {

        $content = '
<form style="width: 450px" method="post">
    <div>
        <label for="title">Заголовок</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="content">Содержимое публикации</label>
        <textarea id="content" name="content" required></textarea>
    </div>
    <div>
        <label for="category">Категория</label>
        <select id="category" name="category" required>
            <option value="">Выберите категорию</option>';

        foreach (get_all_categories() as $category) {
            $content .= "<option value='{$category['id']}'>{$category['name']}</option>";
        }

        $content .= '
        </select>
    </div>
    <div>
        <label for="tag">Теги</label>
        <select id="tag" name="tag[]" multiple>
        </select>
    </div>
    <div>
        <label for="permission">Разрешения для просмотра</label>
        <select id="permission" name="permission[]" multiple>
        
        </select>
    </div>
    <button type="submit">Опубликовать</button>
</form>';
    }
} else {
    $message = 'У вас нет прав для создания публикации';
    http_response_code(403);
}
require_once 'pages/base.php';