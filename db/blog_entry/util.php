<?php
require_once 'db/connection.php';
require_once 'db/tag/util.php';
require_once 'db/permission/built-in.php';

function publish(int   $user_id, string $title, string $content, int $category_id,
                 array $tags, array $permissions): string
{
    $entry_id = create_entry($user_id, $title, $content, $category_id);
    if ($entry_id === -1)
        return 'Не удалось создать публикацию.';

    try {
        foreach ($tags as $tag) {
            add_tag_to_entry($tag, $entry_id);
        }
    } catch (mysqli_sql_exception $exception) {
        return 'Не удалось связать публикацию с тегами. Перейдите к реактированию и попробуйте ещё раз.';
    }

    try {
        foreach ($permissions as $permission) {
            add_permission_to_entry(BUILTIN_PERMISSIONS[$permission] ?? get_permission_by_name($permission)['id'],
                $entry_id);
        }
    } catch (mysqli_sql_exception $exception) {
        return 'Не удалось связать публикацию с разрешениями для просмотра. Перейдите к реактированию и попробуйте ещё раз.';
    }

    return 'Успешно опубликовано.';
}


function create_entry(int $user_id, string $title, string $content, int $category_id): int
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('INSERT INTO blog_entry(title, author_id, content, published, edited, category_id) 
                VALUES (?, ?, ?, NOW(), NOW(), ?)');
        $query->bind_param('sisi', $title, $user_id, $content, $category_id);
        $query->execute();
        get_connection()->commit();
        return $query->insert_id;
    } catch (mysqli_sql_exception $exception) {
        return -1;
    }
}


function get_all_entries(): array
{
    return get_connection()->query('SELECT * FROM blog_entry')->fetch_all(MYSQLI_ASSOC);
}
