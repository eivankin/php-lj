<?php
require_once 'db/blog_entry/util.php';
require_once 'pages/entries/util.php';

/**
 * Главная страница с виджетом популярных публикаций и свежих публикаций среди подписок.
 */

$title = 'Главная страница';
$content = '';

if (isset($_SESSION['user_id'])) {
    $content .= '<h2>Ваши подписки</h2>';
    $content .= '<div class="card-group">';

    $subscription_entries = get_subscription_entries($_SESSION['user_id'], 5);
    if (count($subscription_entries) < 1)
        $content .= '<p>Новых публикаций нет.</p>';
    foreach ($subscription_entries as $entry)
        $content .= make_entry_card($entry);

    $content .= '</div>';
}

$content .= '<h2>Популярные публикации</h2>';
$content .= '<div class="card-group">';
$popular = get_most_popular();
if (count($popular) < 1)
    $content .= '<p>Новых публикаций нет.</p>';
foreach ($popular as $entry)
    $content .= make_entry_card($entry);
$content .= '</div>';
require_once 'pages/base.php';