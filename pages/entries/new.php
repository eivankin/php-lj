<?php
/**
 * Страница создания публикаций.
 * Только пользователи с правом на публикацию материалов могут создавать публикации.
 */

require_once 'pages/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/tag/util.php';

login_required('/entries/new');

$title = 'Добавить публикацию';
if (has_user_permission($_SESSION['user_id'], CAN_PUBLISH)) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) &&
        isset($_POST['content']) && isset($_POST['category'])) {
        $message = publish($_SESSION['user_id'], $_POST['title'], $_POST['content'], $_POST['category'],
            $_POST['tag'] ?? [], $_POST['permission'] ?? [], $_FILES['attachments'] ?? []);
    } else {

        $content = '
<form class="fixed-width" style="width: 450px" method="post" enctype="multipart/form-data">
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

        foreach (get_categories() as $category) {
            $content .= "<option value='{$category['id']}'>{$category['name']}</option>";
        }

        $content .= '
        </select>
    </div>
    <div>
        <label for="tag">Теги</label>
        <select id="tag" name="tag[]" multiple>';

        foreach (get_tags() as $tag) {
            $content .= "<option value='{$tag['id']}'>{$tag['name']}</option>";
        }

        $subscription_id = get_subscription_id($_SESSION['user_id']);
        $content .= "</select>
    </div>
    <div>
        <label for='permission'>Кто может просматривать публикацию</label>
        <select id='permission' name='permission[]' multiple>
            <option value='{$subscription_id}'>Мои подписчики</option>
            <option value='" . ADMIN . "'>Администраторы</option>
            <option value='" . MODERATOR . "'>Модераторы</option>
            <option value='" . CAN_PUBLISH . "'>Авторы других публикаций</option>
        </select>
    </div>
    <div>
        <input type='hidden' name='MAX_FILE_SIZE' value='31457000' />
        <label for='attachments'>Прикрепить изображения</label>
        <input type='file' multiple 
            placeholder='Выберите файл в формате JPEG, PNG или GIF (не более 30 МБ)' 
            id='attachments' name='attachments[]' accept='image/jpeg,image/png,image/gif'>
    </div>
    <button type='submit'>Опубликовать</button>
</form>";
    }
} else {
    $message = 'У вас нет прав для создания публикации';
    http_response_code(403);
}
require_once 'pages/base.php';