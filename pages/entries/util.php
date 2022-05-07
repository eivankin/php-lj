<?php
require_once 'db/user/util.php';

function make_entry_card(array $entry): string {
    $author = get_user($entry['author_id']);
    $views = '';
    if (isset($entry['views_count']))
        $views = "<p>Просмотры: {$entry['views_count']}</p>";

    return "
    <div class='entry-card'>
        <div class='card-header'>{$entry['title']}</div>
        <div class='card-body'>
            <p>Автор: <a href='/users/{$author['id']}'>{$author['username']}</a></p>
            <p>Опубликовано: {$entry['published']}</p>
            {$views}
            <p><a href='/entries/{$entry['id']}'><button>Читать</button></a></p>
        </div>
    </div>
    ";
}


