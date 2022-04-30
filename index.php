<?php
require_once 'db/user/util.php';

session_start();

$urls = array(
    '/login' => 'pages/login.php',
    '/account' => 'pages/account.php',
    '/logout' => 'pages/logout.php',
    '/' => 'pages/index.php'
);

if (isset($urls[$_SERVER['REQUEST_URI']])) {
    require_once $urls[$_SERVER['REQUEST_URI']];
} else {
    http_response_code(404);
    require_once 'pages/404.php';
}
