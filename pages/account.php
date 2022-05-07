<?php
require_once 'db/user/util.php';
require_once 'db/permission/built-in.php';
require_once 'pages/users/util.php';
require_once 'pages/entries/util.php';
require_once 'db/blog_entry/util.php';

login_required('/account');

$title = 'Личный кабинет';

$content = "<a href=\"/users/{$_SESSION['user_id']}/edit\"><button>Редактировать данные профиля</button></a>";
$content .= get_info($_SESSION['user_id']);
$content .= '<h2>Мои публикации</h2>
<div class="card-group">';


foreach (get_entries(null, null, null, $_SESSION['user_id'],
    null, true, 'published', true, 5) as $entry) {
    $content .= make_entry_card($entry);
}

$content .= '</div>
<h2>Мои подписки</h2>
<ol>';

$subscribed_on = get_subscriptions($_SESSION['user_id']);
foreach ($subscribed_on as $user_id) {
    $user = get_user($user_id);
    $content .= "<li><a href='/users/{$user_id}'>{$user['username']}</a>
        <a href='/users/{$user_id[0]}/unsubscribe'><button>Отписаться</button></a></li>";
}
$content .= '</ol>';

require_once 'pages/base.php';
