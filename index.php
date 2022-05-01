<?php
require_once 'pages/util.php';

function handle_url_with_id(array $urls, array $matches, int $id_group = 1, int $action_position = 3) {
    $id = $matches[$id_group];
    $action = explode('/', $_SERVER['REQUEST_URI'])[$action_position];

    if (!isset($action)) {
        require_once $urls[0];
        exit();
    }

    if (isset($urls[$action])) {
        require_once $urls[$action];
        exit();
    }
}

session_start();

$urls = array(
    '/login' => 'pages/login.php',
    '/account' => 'pages/account.php',
    '/logout' => 'pages/logout.php',
    '/' => 'pages/index.php',
    '/forgot-password' => 'pages/forgot_password.php',
    '/entries/' => 'pages/entries/list.php',
    '/entries/new' => 'pages/entries/new.php',
    '/users/' => 'pages/users/list.php',
    '/users/new' =>  'pages/users/new.php',
);

if (isset($urls[$_SERVER['REQUEST_URI']])) {
    require_once $urls[$_SERVER['REQUEST_URI']];
    exit();
}
if (preg_match('/\/users\/(\d+).*/', $_SERVER['REQUEST_URI'], $matches)) {
    handle_url_with_id(array('pages/users/show.php',
        'subscribe' => 'pages/users/subscribe.php',
        'unsubscribe' => 'pages/users/unsubscribe.php',
        'ban' => 'pages/users/ban.php',
        'unban' => 'pages/users/unban.php',
        'delete' => 'pages/users/delete.php',
        'permissions' => 'pages/users/permissions.php'), $matches);
}

if (preg_match('/\/entries\/(\d+).*/', $_SERVER['REQUEST_URI'], $matches)) {
    handle_url_with_id(array('pages/entries/show.php',
        'delete' => 'pages/entries/delete.php',
        'permissions' => 'pages/entries/permissions.php'), $matches);
}


not_found();
