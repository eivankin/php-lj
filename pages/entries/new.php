<?php
require_once 'db/permission/built-in.php';
$title = 'Добавить публикацию';
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}
if (has_permission($_SESSION['user_id'], CAN_PUBLISH)) {
    $content = '<form method="post">
<button type="submit">Опубликовать</button>
</form>';
} else {
    $message = 'У вас нет прав для создания публикации';
}
require_once 'pages/base.php';