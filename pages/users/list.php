<?php
require_once 'db/permission/built-in.php';
require_once 'db/user/util.php';
$title = 'Пользователи';
$is_admin = isset($_SESSION['user_id']) && has_user_permission($_SESSION['user_id'], ADMIN);
$is_moderator = isset($_SESSION['user_id']) && has_user_permission($_SESSION['user_id'], MODERATOR);

$content = '';
if ($is_admin)
    $content .= '<p><a href="./new"><button>Добавить пользователя</button></a></p>';

$content .= '<table><thead><tr>
<th>№</th><th>Имя пользователя</th><th>Последний вход на сайт</th><th>Действия</th>
</tr></thead><tbody>';

foreach (get_all_users() as $user) {
    $actions = '<a href="./' . $user['id'] . '/subscribe">Подписаться</a>' .
        ' | <a href="./' . $user['id'] . '/unsubscribe">Отписаться</a>';
    if ($is_moderator)
        $actions .= ' | <a href="./' . $user['id'] . '/ban">Запретить создавать публикации</a>' .
            ' | <a href="./' . $user['id'] . '/unban">Разрешить создавать публикации</a>';
    if ($is_admin) {
        $actions .= ' | <a href="./' . $user['id'] . '/permissions">Управлять правами</a>' .
            ' | <a href="./' . $user['id'] . '/edit">Редактировать</a>' .
            ' | <a href="./' . $user['id'] . '/delete">Удалить</a>';
    }

    $content .= '<tr><td>' . $user['id'] . '</td><td><a href="./' . $user['id'] . '">' . $user['username'] .
        '</a></td><td>' . $user['last_login'] . '</td><td>' . $actions .  '</td></tr>';
}
$content .= '</tbody></table>';

require_once 'pages/base.php';