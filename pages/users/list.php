<?php
require_once 'db/permission/built-in.php';
$title = 'Пользователи';
$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);
$is_moderator = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], MODERATOR);

$content = '';
if ($is_admin)
    $content .= '<a href="./new">Добавить пользователя</a>';

$content .= '<table><thead><tr>
<th>№</th><th>Имя пользователя</th><th>Последний вход на сайт</th><th>Действия</th>
</tr></thead><tbody>';
$db = get_connection();
$index = 0;
foreach ($db->query('SELECT id, username, last_login FROM user')->fetch_all(MYSQLI_ASSOC) as $user) {
    $actions = '<a href="./' . $user['id'] . '/subscribe">Подписаться</a>' .
        ' | <a href="./' . $user['id'] . '/unsubscribe">Отписаться</a>';
    if ($is_moderator)
        $actions .= ' | <a href="./' . $user['id'] . '/ban">Запретить создавать публикации</a>' .
            ' | <a href="./' . $user['id'] . '/unban">Разрешить создавать публикации</a>';
    if ($is_admin) {
        $actions .= ' | <a href="./' . $user['id'] . '/permissions">Управлять правами</a>' .
            ' | <a href="./' . $user['id'] . '/delete">Удалить</a>';
    }

    $content .= '<tr><td>' . ++$index . '</td><td><a href="./' . $user['id'] . '">' . $user['username'] .
        '</a></td><td>' . $user['last_login'] . '</td><td>' . $actions .  '</td></tr>';
}
$content .= '</tbody></table>';

require_once 'pages/base.php';