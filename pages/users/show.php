<?php
require_once 'pages/users/util.php';
require_once 'pages/entries/util.php';
require_once 'db/blog_entry/util.php';
require_once 'db/permission/util.php';

$title = 'Информация о пользователе';

if (!isset($id))
    $id = null;
handle_page_with_id($id, 'users', '');

if ($id == $_SESSION['user_id']) {
    header('Location: /account');
}

if (has_permission($_SESSION['user_id'], get_subscription_id($id))) {
    $content = "<p><a href='./{$id}/unsubscribe'><button>Отписаться</button></a></p>";
} else {
    $content = "<p><a href='./{$id}/subscribe'><button>Подписаться</button></a></p>";
}

$content .= get_info($id);
$content .= '<h2>Свежие публикации пользователя</h2>
<div class="card-group">';


foreach (get_entries(null, null, null, $id,
    null, true, 'published', true, 5) as $entry) {
    $content .= make_entry_card($entry);
}

$content .= '</div>';

require_once 'pages/base.php';