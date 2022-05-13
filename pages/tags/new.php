<?php
/**
 * Страница создания тегов.
 * Только администраторы могут создавать теги.
 */

require_once 'db/permission/built-in.php';
require_once 'db/tag/util.php';

$title = 'Добавление тега';

if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на создание тегов';
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    try {
        create_tag($_POST['name']);
        $message = 'Тег успешно добавлен';
    } catch (mysqli_sql_exception $exception) {
        $message = 'Не удалось создать тег';
    }
}

$content = '
<form method="post">
    <div>
        <label for="name">Название</label>
        <input type="text" name="name" id="name" required>
    </div>
    <button type="submit">Создать</button>
</form>';

require_once 'pages/base.php';
