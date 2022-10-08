<?php
require_once 'db/blog_entry/util.php';
require_once 'pages/entries/util.php';

/**
 * Главная страница с виджетом популярных публикаций и свежих публикаций среди подписок.
 */

$title = 'Главная страница';
$content = '<h1>Добро пожаловать!</h1>
	<div style="display: flex; justify-content: space-evenly;align-items: center;text-align: center;">
	<div>
	<h3>Есть, чем поделиться?</h3><p><a class="white" href="/entries/new">
		<button>Опубликуйте свой материал!</button>
	</a></p></div>
	<div><h3>Хочется почитать что-нибудь интересное?</h3><p>Ознакомьтесь с подборками, представленными ниже!</p>
	</div></div>';

if (isset($_SESSION['user_id'])) {
    $content .= '<h2>Ваши подписки</h2>';
    $content .= '<div class="card-group">';

    $subscription_entries = get_subscription_entries($_SESSION['user_id'], 5);
    if (count($subscription_entries) < 1)
        $content .= '<p>Новых публикаций нет.</p>';
    foreach ($subscription_entries as $entry)
        $content .= make_entry_card($entry);

    $content .= '</div>
<div style="display: flex; justify-content: center; margin-top: 20px;">
	<a href="/entries/">
		<button style="text-transform: uppercase">Больше публикаций</button>
	</a>
</div>';
}

$content .= '<h2>Популярные публикации</h2>';
$content .= '<div class="card-group">';
$popular = get_most_popular();
if (count($popular) < 1)
    $content .= '<p>Новых публикаций нет.</p>';
foreach ($popular as $entry)
    $content .= make_entry_card($entry);
$content .= '</div>
<div style="display: flex; justify-content: center; margin-top: 20px;">
	<a href="/entries/">
		<button style="text-transform: uppercase">Больше публикаций</button>
	</a>
	</div>';
require_once 'pages/base.php';