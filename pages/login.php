<?php
require_once 'db/user/util.php';
/**
 * Страница входа на сайт.
 * Для входа запрашивается логин (e-mail нужен только для восстановления пароля) и пароль.
 *
 * После успешного входа в сессионной переменной 'user_id' оказывается ID пользователя из базы данных,
 * а также происходит перенаправление в личный кабинет или на страницу, которая запросила аутентификацию.
 */


if (isset($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && isset($_POST['password'])) {
    $user_id = authorize($_POST['login'], $_POST['password']);
    if ($user_id !== -1) {
        $_SESSION['user_id'] = $user_id;
        if (isset($_SESSION['redirect_after_login'])) {
            header('Location: ' . $_SESSION['redirect_after_login']);
            unset($_SESSION['redirect_after_login']);
        } else
            header('Location: /account');
        unset($_SESSION['restore_token']);
        unset($_SESSION['restore_id']);
        exit();
    }
    else
        $message = 'Неправильный логин или пароль';
}


$title = 'Вход на сайт';
$content = '
<form method="post" class="fixed-width">
    <div>
        <label for="login">Логин</label>
        <input type="text" name="login" id="login" required>
    </div>
    <div>
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    
    <button type="submit">Войти</button>
</form>
<p class="fixed-width">
<a href="/forgot-password">Забыли пароль?</a>
</p>';

require_once 'pages/base.php';
