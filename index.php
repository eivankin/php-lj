<?php
/**
 * Этот скрипт отвечает за делегацию конкретных запросов пользователя по фиксированному списку URL
 * соответствующим скриптам-обработчикам. Перенаправление всех запросов к этому скрипту достигается
 * благодаря конфигурации сервера Apache через файл '.htaccess'.
 */

require_once 'pages/util.php';

// Сохранение запрошенного URL без GET-параметров
define('URL', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));


/**
 * Эта функция обрабатывает URL, внутри которых передаётся ID сущности из базы данных.
 * На вход принимается словарь, связывающий действие над сущностью со скриптом-обработчиком.
 */
function handle_url_with_id(array $urls, array $matches, int $id_group = 1, int $action_position = 3)
{
    $id = $matches[$id_group];
    $action = explode('/', URL)[$action_position];

    // Если действие пустое, то оно считается действием просмотра,
    // происходит обращение к первому элементу словаря.
    if (!isset($action)) {
        require_once $urls[0];
        exit();
    }

    // Если
    if (isset($urls[$action])) {
        require_once $urls[$action];
        exit();
    }
}

// Инициализация сессии для текущего пользователя, обратившегося к какой-либо странице,
// чтобы отличать пользователей друг от друга.
session_start();

// Словарь, связывающий URL со скриптом-обработчиком.
// Здесь не перечислены URL, внутри которых передаётся ID сущности из базы данных.
$urls = array(
    '/login' => 'pages/login.php',
    '/account' => 'pages/account.php',
    '/logout' => 'pages/logout.php',
    '/' => 'pages/index.php',
    '/forgot-password' => 'pages/forgot_password.php',
    '/entries/' => 'pages/entries/list.php',
    '/entries/new' => 'pages/entries/new.php',
    '/users/' => 'pages/users/list.php',
    '/users/new' => 'pages/users/new.php',
    '/categories/' => 'pages/categories/list.php',
    '/categories/new' => 'pages/categories/new.php',
    '/tags/' => 'pages/tags/list.php',
    '/tags/new' => 'pages/tags/new.php'
);

// Если URL есть в вышеописанном словаре, то подключаем соответствующий скрипт-обработчик
if (isset($urls[URL])) {
    require_once $urls[URL];
    exit();
}

// Обработка запроса к операции над конкретным пользователем
if (preg_match('/\/users\/(\d+).*/', URL, $matches)) {
    handle_url_with_id(array('pages/users/show.php',
        'subscribe' => 'pages/users/subscribe.php',
        'unsubscribe' => 'pages/users/unsubscribe.php',
        'ban' => 'pages/users/ban.php',
        'unban' => 'pages/users/unban.php',
        'delete' => 'pages/users/delete.php',
        'edit' => 'pages/users/edit.php',
        'permissions' => 'pages/users/permissions.php'), $matches);
}

// Обработка запроса к операции над конкретной публикацией
if (preg_match('/\/entries\/(\d+).*/', URL, $matches)) {
    handle_url_with_id(array('pages/entries/show.php',
        'delete' => 'pages/entries/delete.php',
        'edit' => 'pages/entries/edit.php'), $matches);
}

// Обработка запроса к операции над конкретной категорией
if (preg_match('/\/categories\/(\d+).*/', URL, $matches)) {
    handle_url_with_id(array('pages/categories/show.php',
        'delete' => 'pages/categories/delete.php',
        'edit' => 'pages/categories/edit.php'), $matches);
}

// Обработка запроса к операции над конкретным тегом
if (preg_match('/\/tags\/(\d+).*/', URL, $matches)) {
    handle_url_with_id(array('pages/tags/show.php',
        'delete' => 'pages/tags/delete.php',
        'edit' => 'pages/tags/edit.php'), $matches);
}

// Если для запроса не найден обработчик, то возвращается ошибка 404.
not_found();
