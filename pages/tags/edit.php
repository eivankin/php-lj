<?php
require_once 'db/permission/built-in.php';
require_once 'db/tag/util.php';
require_once 'pages/util.php';

$title = 'Редактирование тега';

if (!isset($id))
    $id = null;

handle_page_with_id($id, 'tags', '/edit');

$tag = get_tag($id);
if (!isset($tag))
    not_found();

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на редактирование тегов';
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    try {
        update_tag($id, $_POST['name']);
        $message = 'Тег успешно обновлён';
    } catch (mysqli_sql_exception $exception) {
        $message = 'Не удалось обновить тег';
    }
} else {

    $content = '
<form class="fixed-width" method="post">
    <div>
        <label for="name">Название</label>
        <input type="text" name="name" id="name" required value="'
        . $tag['name'] .
        '">
    </div>
    <button type="submit">Редактировать</button>
</form>';
}

require_once 'pages/base.php';
