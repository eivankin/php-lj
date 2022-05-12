<?php
require_once 'db/connection.php';
require_once 'db/tag/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/util.php';

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
            add_permission_to_entry($entry_id, $permission);
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


function get_entries(string $title_like = null, string $content_like = null,
                     int    $category_id = null, int $author_id = null,
                     array  $tags_in = null, bool $join_by_and = true,
                     string $order_by_column = null, bool $order_desc = true,
                     int    $limit = null): array
{
    $bind_list = '';
    $filter_condition = array();
    $params = array();

    if (!empty($title_like)) {
        $title_like = "%{$title_like}%";
        $bind_list .= 's';
        $filter_condition[] = 'UPPER(title) LIKE UPPER(?)';
        $params[] = $title_like;
    }

    if (!empty($content_like)) {
        $content_like = "%{$content_like}%";
        $bind_list .= 's';
        $filter_condition[] = 'UPPER(content) LIKE UPPER(?)';
        $params[] = $content_like;
    }

    if (isset($category_id)) {
        $bind_list .= 'i';
        $filter_condition[] = 'category_id = ?';
        $params[] = $category_id;
    }

    if (isset($author_id)) {
        $bind_list .= 'i';
        $filter_condition[] = 'author_id = ?';
        $params[] = $author_id;
    }

    if (isset($tags_in) && count($tags_in) > 0) {
        $bind_list .= str_repeat('s', count($tags_in));
        $filter_condition[] = 'id IN (SELECT entry_id FROM entry_to_tag WHERE tag_id IN (' .
            str_repeat('?,', count($tags_in) - 1) . '?))';
        array_push($params, ...$tags_in);
    }

    $order = make_order_query($order_by_column, $order_desc);
    $limit_query = make_limit_query($limit);

    if (empty($bind_list))
        return get_connection()->query("SELECT * FROM blog_entry{$order}")->fetch_all(MYSQLI_ASSOC);

    $filter_condition = ' WHERE ' . join($join_by_and ? ' AND ' : ' OR ', $filter_condition);
    $query = get_connection()->prepare("SELECT * FROM blog_entry{$filter_condition}{$order}{$limit_query}");
    $query->bind_param($bind_list, ...$params);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}


function get_entry(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM blog_entry WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

function edit(int    $entry_id, string $title,
              string $content, int $category_id,
              array  $tags, array $permissions,
              array  $old_tags, array $old_permissions): string
{
    if (!update_entry($entry_id, $title, $content, $category_id))
        return 'Не удалось обновить публикацию';

    try {
        foreach (array_diff($old_tags, $tags) as $tag_to_remove)
            remove_tag_from_entry($tag_to_remove, $entry_id);

        foreach (array_diff($tags, $old_tags) as $tag_to_add)
            add_tag_to_entry($tag_to_add, $entry_id);
    } catch (mysqli_sql_exception $exception) {
        return 'Не удалось обновить теги публикации';
    }

    try {
        foreach (array_diff($old_permissions, $permissions) as $permission_to_remove)
            remove_permission_from_entry($entry_id, $permission_to_remove);

        foreach (array_diff($permissions, $old_permissions) as $permission_to_add)
            add_permission_to_entry($entry_id, $permission_to_add);

    } catch (mysqli_sql_exception $exception) {
        return 'Не удалось обновить разрешения для просмотра публикации';
    }

    return 'Публикация успешно обновлена';
}

function update_entry(int $entry_id, string $title, string $content, int $category_id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('UPDATE blog_entry SET title = ?, content = ?, category_id = ?, edited = NOW() WHERE id = ?');
        $query->bind_param('ssii', $title, $content, $category_id, $entry_id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

function delete_entry(int $id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('DELETE FROM blog_entry WHERE id = ?');
        $query->bind_param('i', $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

function get_most_popular(int $limit = 5): array
{
    $query = get_connection()->prepare('SELECT blog_entry.*, COUNT(entry_id) AS views_count from blog_entry 
        INNER JOIN entry_view ev on blog_entry.id = ev.entry_id 
                                                    GROUP BY entry_id ORDER BY views_count DESC LIMIT ?');
    $query->bind_param('i', $limit);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_subscription_entries(int $user_id, int $limit = null, string $order_by_column = 'published', bool $order_desc = true): array
{
    $subscribed_on = get_subscriptions($user_id);
    if (count($subscribed_on) == 0)
        return [];

    $params = str_repeat('?,', count($subscribed_on) - 1) . '?';
    $bind_list = str_repeat('i', count($subscribed_on));

    $limit_query = make_limit_query($limit);
    $order = make_order_query($order_by_column, $order_desc);

    $query = get_connection()->prepare("SELECT * FROM blog_entry WHERE author_id IN ({$params}){$order}{$limit_query}");
    $query->bind_param($bind_list, ...$subscribed_on);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}