<?php
require_once 'db/user/util.php';
require_once 'pages/util.php';
require_once 'db/permission/util.php';

function get_info(int $user_id): string
{
    $user = get_user($user_id);
    if (!isset($user)) {
        not_found();
    }

    $subscribers_count = get_subscribers_count($user_id);

    $content = '<h1>Информация о пользователе</h1>';
    $content .= "<table>
<tbody>
    <tr>
        <th>Имя пользователя</th>
        <td>{$user['username']}</td>
    </tr>
    <tr>
        <th>Последний вход на сайт</th>
        <td>{$user['last_login']}</td>
    </tr>
    <tr>
        <th>Количество подписчиков</th>
        <td>{$subscribers_count}</td>
    </tr>
</tbody>
</table>";
    return $content;
}

/**
 * Общая часть для подписки и отписки от пользователя, вынесена отдельно для исключения повторений.
 */
function handle_subscription($id, string $action = '/subscribe'): int
{
    require_once 'pages/util.php';
    require_once 'db/permission/built-in.php';

    handle_page_with_id($id, 'users', $action);
    $user = get_user($id);
    if (!isset($user))
        not_found();

    return get_subscription_id($id);
}