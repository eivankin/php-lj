<?php
/**
 * Страница редактирования пользователя.
 * Только администраторы могут удалять редактировать других пользователей.
 * Для редактирования самого себя пользователю (в т.ч. администратору) требуется ввести пароль.
 */

require_once 'pages/util.php';
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';

if (!isset($id))
    $id = null;
handle_page_with_id($id, 'users', '/edit');

$title = 'Редактирование пользователя';
$is_the_same_user = $_SESSION['user_id'] == $id;
$user = get_user($id);

if (!$is_the_same_user && !has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на редактирования данных этого пользователя';
    http_response_code(403);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) &&
    isset($_POST['login'])) {
    if ($is_the_same_user && !verify_password($_SESSION['user_id'], $_POST['password'])) {
        $message = 'Введён неправильный пароль';
    } elseif (update_user($id, $_POST['login'], $_POST['email']))
        $message = 'Данные пользователя успешно обновлены';
    else
        $message = 'Не удалось обновить данные пользователя';
} else {
    if (!isset($user))
        not_found();

    $content = '<form class="fixed-width" method="post">
    <div>
        <label for="login">Логин</label>
        <input type="text" name="login" id="login" value="' . $user['username'] . '" required>
    </div>
    <div>
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" value="' . $user['email'] . '" required>
    </div>';
    if ($is_the_same_user) {
        $content .= '
        <div>
            <label for="password">Текущий пароль</label>
            <input type="password" name="password" id="password" required>
        </div>';
    }
    $content .= '
    <button type="submit">Редактировать</button>
</form>';
}
require_once 'pages/base.php';