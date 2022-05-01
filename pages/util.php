<?php
function login_required(string $redirect_to) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $redirect_to;
        header('Location: /login');
        exit();
    }
}

function not_found() {
    http_response_code(404);
    require_once 'pages/404.php';
    exit();
}

function handle_page_with_id($id, string $dir, string $action, bool $login_required = true) {
    if (!isset($id))
        not_found();
    if ($login_required)
        login_required('/' . $dir . '/' . $id . $action);
}