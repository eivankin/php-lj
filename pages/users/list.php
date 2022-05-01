<?php
require_once 'db/permission/built-in.php';
$title = 'Пользователи';
$is_admin = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], ADMIN);
$is_moderator = isset($_SESSION['user_id']) && has_permission($_SESSION['user_id'], MODERATOR);

$content = '<table><thead><tr>
<th>№</th><th>Имя пользователя</th><th>Последний вход на сайт</th><th>Действия</th>
</tr></thead><tbody>';
$db = get_connection();
$index = 0;
foreach ($db->query('SELECT id, username, last_login FROM user')->fetch_all() as $user) {
    $actions = '<a href="./' . $user[0] . '/subscribe">Подписаться</a>';
    if ($is_moderator)
        $actions .= ' | <a href="./' . $user[0] . '/ban">Запретить создавать публикации</a>' .
            ' | <a href="./' . $user[0] . '/unban">Разрешить создавать публикации</a>';
    if ($is_admin) {
        $actions .= ' | <a href="./' . $user[0] . '/permissions">Управлять правами</a>' .
            ' | <a href="./' . $user[0] . '/delete">Удалить</a>';
    }

    $content .= '<tr><td>' . ++$index . '</td><td><a href="./' . $user[0] . '">' . $user[1] .
        '</a></td><td>' . $user[2] . '</td><td>' . $actions .  '</td></tr>';
}
$content .= '</tbody></table>';

require_once 'pages/base.php';