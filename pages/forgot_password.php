<?php
require_once 'db/user/util.php';

/**
 * Страница восстановления пароля.
 *
 * Восстановление происходит в три этапа:
 * 1. Неаутентифицированный пользователь вводит e-mail.
 * 2. Если пользователь с таким e-mail существует, то ему на почту отправляется код восстановления.
 * 3. Если пользователь вводит правильный код, то ему предлагается задать новый пароль. В случае смены пароля отправляется оповещение.
 *
 * Прогресс по восстановлению пароля отслеживается за счёт сессионных переменных.
 * После окончания процедуры или удачного входа прогресс по восстановлению пароля сбрасывается.
 * Код (шестизначное число, в будущем можно перейти на генерацию более длинных чисел или, например, случайных слов,
 * которые будет так же легко запомнить в случае отсутствия возможности копирования) для сброса пароля хранится в хэшированном виде.
 */

const PSWD_FORM = '
<form class="fixed-width" method="post">
    <div>
        <label for="password">Новый пароль</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="password2">Подтвердите пароль</label>
        <input type="password" id="password2" name="password2" required>
    </div>
    <button type="submit">Установить пароль</button>
</form class="fixed-width">';
const TOKEN_FORM = '
<form class="fixed-width" method="post">
    <div>
        <label for="token">Код</label>
        <input type="text" id="token" name="token" required>
    </div>
    <button type="submit">Сбросить пароль</button>
</form class="fixed-width">';


if (isset($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $user_id = get_user_by_email($_POST['email']);
    if (!isset($user_id))
        $message = 'Пользователь с таким e-mail не найден';
    else {
        $message = 'Код для сброса пароля отправлен на указанную почту';
        try {
            $token = random_int(100000, 999999);
            $_SESSION['restore_token'] = password_hash($token, PASSWORD_DEFAULT);
            $_SESSION['restore_id'] = $user_id;
            mail($_POST['email'], 'Восстановление пароля на сайте ' . $_SERVER['HTTP_HOST'],
                'Для сброса пароля введите код ' . $token . ' в соответсвующей форме. ' .
                'Если вы не запрашивали сброс пароля, то проигнорируйте данное письмо.');

            $content = TOKEN_FORM;
        } catch (Exception $e) {
            $message = 'Невозможно восстановить пароль, пожалуйста, свяжиетсь с администратором для сброса пароля вручную.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && isset($_SESSION['restore_token'])) {
    if (!password_verify($_POST['token'], $_SESSION['restore_token'])) {
        $message = 'Введён неправильный код';
        $content = TOKEN_FORM;
    } else {
        unset($_SESSION['restore_token']);
        $content = PSWD_FORM;
    }
} elseif (isset($_SESSION['restore_id']) && $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['password']) && isset($_POST['password2'])) {
    if ($_POST['password'] != $_POST['password2']) {
        $message = 'Пароли должны совпадать';
        $content = PSWD_FORM;
    } else {
        set_password($_SESSION['restore_id'], $_POST['password']);
        unset($_SESSION['restore_id']);
        $message = 'Новый пароль успешно задан, вход на сайт по нему доступен';
        mail($_POST['email'], 'Пароль на сайте ' . $_SERVER['HTTP_HOST'] . ' изменён',
            'Пароль для входа на сайт был успешно изменён.');
    }
} elseif (isset($_SESSION['restore_token'])) {
    $message = 'Код для сброса пароля отправлен на указанную почту';
    $content = TOKEN_FORM;
} elseif (isset($_SESSION['restore_id'])) {
    $content = PSWD_FORM;
}

$title = 'Восстановление пароля';
if (!isset($content)) {
    $content = '
<form class="fixed-width" method="post">
<div>
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required>
</div>
<button type="submit">Восстановить пароль</button>
</form class="fixed-width">
';
}
require_once 'pages/base.php';