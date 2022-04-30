<?php
if (isset($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {

    $message = 'Пользователь с таким e-mail не найден';
}

$title = 'Восстановление пароля';
$content = '
<form method="post">
<div>
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>
</div>
<button type="submit">Восстановить пароль</button>
</form>
';
require_once 'pages/base.php';