<?php
/**
 * Страница редактирования публикации.
 * Публикации могут редактировать только их авторы и администраторы.
 */

require_once 'pages/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/blog_entry/views.php';
require_once 'db/user/util.php';
require_once 'db/category/util.php';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'entries', '/edit');
$entry = get_entry($id);
if (!isset($entry)) {
    not_found();
}

$title = 'Редактирование публикации';

if ($_SESSION['user_id'] == $entry['author_id'] || has_user_permission($_SESSION['user_id'], ADMIN)) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) &&
        isset($_POST['content']) && isset($_POST['category'])) {
        $message = edit($entry['id'], $_POST['title'], $_POST['content'], $_POST['category'],
            $_POST['tag'] ?? [], $_POST['permission'] ?? [],
            $_POST['old_tags'] ?? [], $_POST['old_permissions'] ?? [],
            $_POST['attachments_to_delete'] ?? [], $_FILES['new_attachments'] ?? []);
    } else {

        $content = '
<form class="fixed-width" style="width: 450px" method="post" enctype="multipart/form-data">
    <div>
        <label for="title">Заголовок</label>
        <input type="text" id="title" value="' . $entry['title'] . '" name="title" required>
    </div>
    <div>
        <label for="content">Содержимое публикации</label>
        <textarea id="content" name="content" required>' . $entry['content'] . '</textarea>
    </div>
    <div>
        <label for="category">Категория</label>
        <select id="category" name="category" required>
            <option value="">Выберите категорию</option>';

        foreach (get_categories() as $category) {
            $selected = ($category['id'] == $entry['category_id']) ? ' selected' : '';
            $content .= "<option value='{$category['id']}'{$selected}>{$category['name']}</option>";
        }

        $content .= '
        </select>
    </div>
    <div>
        <label for="tag">Теги</label>
        <select id="tag" name="tag[]" multiple>';

        $selected_tags = array();
        foreach (get_tags() as $tag) {
            $selected = '';
            if (has_tag($entry['id'], $tag['id'])) {
                $selected = ' selected';
                $selected_tags[] = $tag['id'];
            }
            $content .= "<option value='{$tag['id']}'{$selected}>{$tag['name']}</option>";
        }

        $subscription_id = get_subscription_id($_SESSION['user_id']);
        $permissions = array($subscription_id => 'Мои подписчики',
            ADMIN => 'Администраторы',
            MODERATOR => 'Модераторы',
            CAN_PUBLISH => 'Авторы других публикаций');

        $id_to_select = array();
        $selected_permissions = array();

        foreach (array_keys($permissions) as $id) {
            $id_to_select[$id] = has_entry_permission($entry['id'], $id);
            if ($id_to_select[$id])
                $selected_permissions[] = $id;
        }

        $content .= "</select>
    </div>
    <div>
        <label for='permission'>Кто может просматривать публикацию</label>
        <select id='permission' name='permission[]' multiple>";
        foreach ($permissions as $id => $name) {
            $selected = ($id_to_select[$id]) ? ' selected' : '';
            $content .= "<option value='{$id}'{$selected}>{$name}</option>";
        }

        $content .= "</select>
    </div>";

        foreach ($selected_tags as $id) {
            $content .= "<input type='hidden' name='old_tags[]' value='$id'>";
        }

        foreach ($selected_permissions as $id) {
            $content .= "<input type='hidden' name='old_permissions[]' value='$id'>";
        }

        $attachments = get_entry_attachments($entry['id']);
        if (!empty($attachments)) {

            $content .= "
            <div>
                <label for='attachments_to_delete'>Выберите вложения для удаления</label>
                <select id='attachments_to_delete' name='attachments_to_delete[]' multiple>
                    ";

            foreach ($attachments as $attachment) {
                $content .= "<option value='{$attachment['id']}'>{$attachment['url']}</option>";
            }

                $content .= "
                </select>
            </div>
        ";
        }

        $content .= "
        <div>
            <input type='hidden' name='MAX_FILE_SIZE' value='31457000' />
            <label for='attachments'>Прикрепить изображения</label>
            <input type='file' multiple 
                placeholder='Выберите файл в формате JPEG, PNG или GIF (не более 30 МБ)' 
                id='attachments' name='new_attachments[]' accept='image/jpeg,image/png,image/gif'>
        </div>";

        $content .= "<button type='submit'>Опубликовать</button></form>";
    }
} else {
    $message = 'У вас нет прав для редактирования этой публикации';
    http_response_code(403);
}
require_once 'pages/base.php';