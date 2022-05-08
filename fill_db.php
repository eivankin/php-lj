<?php
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/category/util.php';
require_once 'db/tag/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/blog_entry/views.php';

echo create_user('example@mail.ru', 'admin', 'admin');
add_permission_to_user(1, ADMIN);
add_permission_to_user(1, MODERATOR);
add_permission_to_user(1, CAN_PUBLISH);

$subscription_on_admin = get_subscription_id(1);

echo create_user('user@localhost', 'user', 'user');
add_permission_to_user(2, CAN_PUBLISH);
add_permission_to_user(2, $subscription_on_admin);

create_category('Тестовая категория', null);
create_category('Тестовая подкатегория', 1);

create_tag('Тестовый тег');

publish(1, 'Тестовая публикация для подписчиков', 'Привет, подписчики!', 1, [], [$subscription_on_admin]);
publish(2, 'Тестовая публикация для всех', 'Привет, мир!', 2, [1], []);

create_view(1, 1);
create_view(1, 2);