<?php
require_once 'pages/util.php';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

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
    '/categories/' => 'pages/categories/list.php',
    '/categories/new' => 'pages/categories/new.php',
    '/tags/' => 'pages/tags/list.php',
    '/tags/new' => 'pages/tags/new.php'
);

if (isset($urls[$url])) {
    require_once $urls[$url];
    exit();
}
if (preg_match('/\/users\/(\d+).*/', $url, $matches)) {
    handle_url_with_id(array('pages/users/show.php',
        'subscribe' => 'pages/users/subscribe.php',
        'unsubscribe' => 'pages/users/unsubscribe.php',
        'ban' => 'pages/users/ban.php',
        'unban' => 'pages/users/unban.php',
        'delete' => 'pages/users/delete.php',
        'edit' => 'pages/users/edit.php',
        'permissions' => 'pages/users/permissions.php'), $matches);
}

if (preg_match('/\/entries\/(\d+).*/', $url, $matches)) {
    handle_url_with_id(array('pages/entries/show.php',
        'delete' => 'pages/entries/delete.php',
        'edit' => 'pages/entries/edit.php'), $matches);
}

if (preg_match('/\/categories\/(\d+).*/', $url, $matches)) {
    handle_url_with_id(array('pages/categories/show.php',
        'delete' => 'pages/categories/delete.php',
        'edit' => 'pages/categories/edit.php'), $matches);
}

if (preg_match('/\/tags\/(\d+).*/', $url, $matches)) {
    handle_url_with_id(array('pages/tags/show.php',
        'delete' => 'pages/tags/delete.php',
        'edit' => 'pages/tags/edit.php'), $matches);
}


not_found();
