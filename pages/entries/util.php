<?php
require_once 'db/user/util.php';
require_once 'db/category/util.php';
require_once 'db/tag/util.php';
require_once 'pages/util.php';

/**
 * Эта функция возвращает список тегов данной публикации в виде строки, содержащей HTML-ссылки на них.
 */
function get_entry_tags_as_str(array $entry): string {
    return get_str_or_no(join(' | ', array_map(function ($t) {
        return "<a href='/entries/?tags[]={$t['id']}'>{$t['name']}</a>";
    }, get_entry_tags($entry['id']))));
}

/**
 * Эта функция генерирует HTML-карточку публикации на основе словаря с атрибутами публикации.
 */
function make_entry_card(array $entry): string
{
    $author = get_user($entry['author_id']);
    $category = get_category($entry['category_id']);
    $tags = get_entry_tags_as_str($entry);
    $views = '';
    if (isset($entry['views_count']))
        $views = "<p>Просмотры: {$entry['views_count']}</p>";

    $comments = '';
    if (isset($entry['comments_count']))
        $comments = "<p>Комментарии: {$entry['comments_count']}</p>";

    return "
    <div class='entry-card'>
        <div class='card-header'>{$entry['title']}</div>
        <div class='card-body'>
            <p>Автор: <a href='/users/{$author['id']}'>{$author['username']}</a></p>
            <p>Опубликовано: {$entry['published']}</p>
            <p>Категория: <a href='/entries/?category={$category['id']}'>{$category['name']}</a></p>
            <p>Теги: {$tags}</p>
            {$views}
            {$comments}
            <p><a href='/entries/{$entry['id']}'><button>Читать</button></a></p>
        </div>
    </div>
    ";
}

function can_view(int $user_id, array $permissions, array $entry): bool
{
    return $user_id == $entry['author_id'] || has_any_permission($user_id, $permissions);
}

function make_image(array $attachment): string
{
    return "
    <input class='img-controller' type='checkbox' id='img-controller-{$attachment['id']}'>
    <label class='img-container' for='img-controller-{$attachment['id']}'>
        <img class='img-element' src='{$attachment['url']}' alt=''>
        <span class='close-btn'>&times;</span>
    </label>
    ";
}