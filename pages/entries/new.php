<?php
login_required('/entries/new');

require_once 'db/permission/built-in.php';
$title = 'Добавить публикацию';
if (has_permission($_SESSION['user_id'], CAN_PUBLISH)) {
    $content = '<form method="post">' .
        '<button type="submit">Опубликовать</button></form>';
} else {
    $message = 'У вас нет прав для создания публикации';
}
require_once 'pages/base.php';