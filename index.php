<?php

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
);

if (isset($urls[$_SERVER['REQUEST_URI']])) {
    require_once $urls[$_SERVER['REQUEST_URI']];
    exit();
}
if (preg_match('/\/users\/(\d+).*/', $_SERVER['REQUEST_URI'], $matches)) {
    $user_urls = array(
        'subscribe' => 'pages/users/subscribe.php',
        'ban' => 'pages/users/ban.php',
        'unban' => 'pages/users/unban.php',
        'permissions' => 'pages/users/permissions.php'
    );
    $user_id = $matches[1];
    $action = explode('/', $_SERVER['REQUEST_URI'])[3];

    if (!isset($action)) {
        require_once 'pages/users/show.php';
        exit();
    }

    if (isset($user_urls[$action])) {
        require_once $user_urls[$action];
        exit();
    }
}

if (preg_match('/\/entries\/(\d+).*/', $_SERVER['REQUEST_URI'], $matches)) {
    $entry_urls = array(
        'delete' => 'pages/entries/delete.php',
        'permissions' => 'pages/entries/permissions.php'
    );
    $entry_id = $matches[1];
    $action = explode('/', $_SERVER['REQUEST_URI'])[3];

    if (!isset($action)) {
        require_once 'pages/entries/show.php';
        exit();
    }

    if (isset($entry_urls[$action])) {
        require_once $entry_urls[$action];
        exit();
    }
}


http_response_code(404);
require_once 'pages/404.php';
