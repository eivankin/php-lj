<?php
$title = 'Главная страница';
$content = '<h1>Добро пожаловать</h1>';

if (isset($_SESSION['user_id'])) {
    $content .= '<h2>Ваши подписки</h2>';
}

$content .= '<h2>Популярные публикации</h2>';
require_once 'pages/base.php';