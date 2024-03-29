<?php
/**
 * Страница создания пользователя.
 * Только администраторы могут создавать новых пользователей.
 * В будущем можно будет разрешить самостоятельную регистрацию пользователей.
 */

require_once 'pages/util.php';
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';
login_required('/users/new');

$title = 'Создание пользователя';
if (!has_user_permission($_SESSION['user_id'], ADMIN)) {
    $message = 'У вас нет прав на создание пользователей';
    http_response_code(403);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) &&
    isset($_POST['login']) && isset($_POST['password'])) {
    $message = create_user($_POST['email'], $_POST['login'], $_POST['password']);
} else {
    $content = '<form class="fixed-width" method="post">
    <div>
        <label for="login">Логин</label>
        <input type="text" name="login" id="login" required>
    </div>
    <div>
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div>
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    <button type="submit">Создать</button>
</form>';
}
require_once 'pages/base.php';

