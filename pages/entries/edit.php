<?php
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

if ($_SESSION['user_id'] == $entry['author_id'] || has_permission($_SESSION['user_id'], ADMIN)) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) &&
        isset($_POST['content']) && isset($_POST['category'])) {
        $message = edit($entry['id'], $_POST['title'], $_POST['content'], $_POST['category'],
            $_POST['tag'] ?? [], $_POST['permission'] ?? [],
            $_POST['old_tags'] ?? [], $_POST['old_permissions'] ?? []);
    } else {

        $content = '
<form class="fixed-width" style="width: 450px" method="post">
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

        foreach (get_all_categories() as $category) {
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
        foreach (get_all_tags() as $tag) {
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

        $content .= "<button type='submit'>Опубликовать</button></form>";
    }
} else {
    $message = 'У вас нет прав для редактирования этой публикации';
    http_response_code(403);
}
require_once 'pages/base.php';