<?php
require_once 'db/user/util.php';

/**
 * Эта функция генерирует HTML-карточку комментария к публикации на основе словаря с его атрибутами.
 */
function make_comment_card(array $comment, bool $can_delete, bool $is_author): string
{
    $author = get_user($comment['author_id']);
    $actions = '';
    if ($can_delete || $is_author) {
        $actions .= "
        <form style='display: inline-block' method='post' action='/entries/{$comment['entry_id']}/comments/delete'>
            <input type='hidden' name='comment_id' value='{$comment['id']}'>
            <button type='submit'>Удалить</button>
        </form>";
    }

    if ($is_author) {
        $body = "
        <form style='display: inline-block' method='post' action='/entries/{$comment['entry_id']}/comments/edit'>
            <input type='text' name='comment_text' value='{$comment['text']}'>
            <input type='hidden' name='comment_id' value='{$comment['id']}'>
            <button type='submit'>Редактировать</button>
        </form>";
    } else {
        $body = "{$comment['text']}";
    }


    return "
        <p>
            {$body}{$actions}<br>
            &mdash; <a href='/user/{$comment['author_id']}'>{$author['username']}</a>, 
            {$comment['published']}
        </p>";
}
