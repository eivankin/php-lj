<?php

require_once 'db/user/util.php';

/**
 * Страница регистрации нового пользователя.
 *
 * Восстановление происходит в три этапа:
 * 1. Неаутентифицированный пользователь вводит e-mail.
 * 2. Если пользователь с таким e-mail НЕ существует, то на указанную почту отправляется код подтверждения.
 * 3. Если пользователь вводит правильный код, то ему предлагается задать логин и пароль новой учётной записи. В случае успешной регистрации отправляется оповещение на указанную почту.
 *
 * Прогресс по процедуре регистрации отслеживается за счёт сессионных переменных.
 * После окончания процедуры прогресс по восстановлению пароля сбрасывается.
 * Код (шестизначное число, в будущем можно перейти на генерацию более длинных чисел или, например, случайных слов,
 * которые будет так же легко запомнить в случае отсутствия возможности копирования) подтверждения хранится в хэшированном виде.
 */

const EMAIL_FORM = '
<form class="fixed-width" method="post">
    <div>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="reg_email" required>
    </div>
    <button type="submit">Подтвердить e-mail</button>
</form class="fixed-width">';

const REG_FORM = '
<form class="fixed-width" method="post">
    <div>
        <label for="login">Логин</label>
        <input type="text" name="login" id="login" required>
    </div>
    <div>
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div>
        <label for="password2">Подтвердите пароль</label>
        <input type="password" id="password2" name="password2" required>
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>';

const TOKEN_FORM = '
<form class="fixed-width" method="post">
    <div>
        <label for="token">Код</label>
        <input type="text" id="token" name="token" required>
    </div>
    <button type="submit">Зарегистрироваться</button>
</form class="fixed-width">';

// Перенаправить аутентифицированного пользователя на страницу профиля
// Сброс пароля доступен только для пользователей, не прошедших процедуру входа на сайт
if (isset($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reg_email'])) {
    $user_id = get_user_by_email($_POST['reg_email']);
    if (isset($user_id))
        $message = 'Пользователь с таким e-mail уже зарегистрирован';
    else {
        $message = 'Код подтверждения отправлен на указанную почту';
        try {
            $token = random_int(100000, 999999);
            $_SESSION['reg_token'] = password_hash($token, PASSWORD_DEFAULT);
            $_SESSION['reg_email'] = $_POST['reg_email'];
            mail($_POST['reg_email'], 'Регистрация на сайте ' . $_SERVER['HTTP_HOST'],
                'Для подтверждения e-mail введите код ' . $token . ' в соответствующей форме. ' .
                'Если вы не запрашивали подтверждение e-mail для регистрации, то проигнорируйте данное письмо.');

            $content = TOKEN_FORM;
        } catch (Exception $e) {
            $message = 'Невозможно зарегистрироваться, пожалуйста, свяжитесь с администратором для создания нового пользователя вручную.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && isset($_SESSION['reg_token'])) {
    if (!password_verify($_POST['token'], $_SESSION['reg_token'])) {
        $message = 'Введён неправильный код';
        $content = TOKEN_FORM;
    } else {
        unset($_SESSION['reg_token']);
        $_SESSION['token_verified'] = true;
        $content = REG_FORM;
    }
} elseif (isset($_SESSION['reg_email']) && isset($_SESSION['token_verified']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['password']) && isset($_POST['password2']) && isset($_POST['login'])) {
    if ($_POST['password'] != $_POST['password2']) {
        $message = 'Пароли должны совпадать';
        $content = REG_FORM;
    } else {
        $message = create_user($_SESSION['reg_email'], $_POST['login'], $_POST['password']);
        if ($message == CREATION_SUCCESS_MSG) {
            mail($_SESSION['reg_email'], 'Регистрация на сайте ' . $_SERVER['HTTP_HOST'],
                'Процедура регистрации успешно пройдена, для входа на сайт используйте этот e-mail и пароль, указанный вами в форме регистрации.');
            unset($_SESSION['token_verified']);
            unset($_SESSION['reg_email']);
            $content = '';
        } else {
            $content = REG_FORM;
        }
    }
} elseif (isset($_SESSION['reg_token'])) {
    $message = 'Код подтверждения отправлен на указанную почту';
    $content = TOKEN_FORM;
} elseif (isset($_SESSION['reg_email'])) {
    $content = REG_FORM;
}

$title = 'Регистрация';
if (!isset($content)) {
    $content = EMAIL_FORM;
}
require_once 'pages/base.php';