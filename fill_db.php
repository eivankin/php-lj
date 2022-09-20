<?php
/**
 * Этот скрипт заполняет базу данных тестовым содержимым для демонстрации работы сайта.
 * Перед выполнением требуется создать базу данных 'blog' с кодировкой utf8mb4_0900_ai_ci,
 * например, через phpMyAdmin.
 */
require_once 'db/connection.php';

// Создание таблиц в базе данных 'blog'
foreach (mb_split(';' . PHP_EOL, file_get_contents('create_tables.sql')) as $table_query)
    get_connection()->query($table_query);

require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';
require_once 'db/tag/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/blog_entry/views.php';
require_once 'db/comment/util.php';
require_once 'db/blog_entry/attachments.php';

// Создание аккаунта администратора и выдача ему прав
echo create_user('example@mail.ru', 'admin', 'admin') . PHP_EOL;
add_permission_to_user(1, ADMIN);
add_permission_to_user(1, MODERATOR);
add_permission_to_user(1, CAN_PUBLISH);
add_permission_to_user(1, CAN_COMMENT);

$subscription_on_admin = get_subscription_id(1);

// Создание аккаунта обычного пользователя,
// выдача ему права на публикацию материалов и подписки на администратора
echo create_user('user@localhost', 'user', 'user');
add_permission_to_user(2, CAN_PUBLISH);
add_permission_to_user(2, CAN_COMMENT);
add_permission_to_user(2, $subscription_on_admin);

// Создание категорий
create_category('Наука', null);
create_category('Технологии', 1);
create_category('Программирование', 2);

// Создание тегов
create_tag('PHP');
create_tag('HTML');
create_tag('CSS');

// Создание публикаций
echo publish(1, 'Тестовая публикация для подписчиков', 'Привет, подписчики!', 3, [1, 2, 3],
    [$subscription_on_admin], []) . PHP_EOL;
sleep(1); // Пауза между созданием материалов для разного времени публикации
echo publish(2, 'Тестовая публикация для всех', 'Привет, мир!', 2, [1], [], []) . PHP_EOL;

// Создание просмотров публикации
create_view(1, 1);
create_view(1, 2);

// Создание комментариев
create_comment(1, 2, 'Хорошая публикация');