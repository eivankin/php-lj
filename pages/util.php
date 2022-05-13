<?php

/**
 * Функция, проверяющая, что пользователь аутентифицирован.
 * Неаутентифицированный пользователь перенаправляется на страницу входа на сайт.
 */
function login_required(string $redirect_to) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $redirect_to;
        header('Location: /login');
        exit();
    }
}

/**
 * Эта функция подключает HTML-шаблон, сообщающий об ошибке 404,
 * возвращает соответствующую ошибку в ответе пользователю и прекращает дальнейшую работу скриптов.
 */
function not_found() {
    http_response_code(404);
    require_once 'pages/404.php';
    exit();
}

/**
 * Эта функция обрабатывает запрос к странице конкретной сущности из базы данных по её ID: проверяется, что ID задан
 * и что пользователь аутентифицирован, если это требуется.
 */
function handle_page_with_id($id, string $dir, string $action, bool $login_required = true) {
    if (!isset($id))
        not_found();
    if ($login_required)
        login_required('/' . $dir . '/' . $id . $action);
}