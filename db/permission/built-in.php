<?php
require_once 'db/permission/util.php';
define('ADMIN', get_or_create_permission('admin',
    'Роль администратора, дающая доступ к панели администратора'));
define('MODERATOR', get_or_create_permission('moderator',
    'Роль модератора, позваляющая удалять публикации и управлять разрешением на их создание'));
define('CAN_PUBLISH', get_or_create_permission('can_publish',
    'Разрешение на публикацию материалов от своего лица'));