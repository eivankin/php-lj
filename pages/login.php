<?php
if (isset($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']) && isset($_POST['password'])) {
    $user_id = authorize($_POST['login'], $_POST['password']);
    if ($user_id !== -1) {
        $_SESSION['user_id'] = $user_id;
        header('Location: /account');
        exit();
    }
    else
        $message = 'Неправильный логин или пароль';
}


$title = 'Вход на сайт';
$content = '
<form method="post">
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
