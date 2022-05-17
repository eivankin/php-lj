<?php
require_once 'db/connection.php';
require_once 'db/tag/util.php';
require_once 'db/permission/built-in.php';
require_once 'db/util.php';

/**
 * Эта функция выполняет все необходимые действия для публикации материала:
 * создаёт сам материал, связывает с ним теги и разрешения.
 *
 * Принимает на вход атрибуты публикации, массив ID тегов и прав для просмотра публикации.
 *
 * Возвращает сообщение с информацией об успешности публикации материала.
 */
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
        return 'Не удалось связать публикацию с тегами. Перейдите к редактированию и попробуйте ещё раз.';
    }

    try {
        foreach ($permissions as $permission) {
            add_permission_to_entry($entry_id, $permission);
        }
    } catch (mysqli_sql_exception $exception) {
        return 'Не удалось связать публикацию с разрешениями для просмотра. Перейдите к редактированию и попробуйте ещё раз.';
    }

    return 'Успешно опубликовано.';
}

/**
 * Эта функция создаёт публикацию в базе данных, не затрагивая никакие связанные сущности.
 *
 * Возвращает ID созданной публикации в случае успеха, иначе -1.
 */
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

/**
 * Эта функция возвращает список из всех публикаций, соответствующих переданным параметрам фильтрации и
 * ограничениям по количеству, а также отсортированный согласно переданным параметрам.
 *
 * Возвращает ассоциативный массив из публикаций.
 */
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

/**
 * Эта функция возвращает публикацию по её ID.
 *
 * Если публикация не найдена, возвращает null.
 */
function get_entry(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM blog_entry WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}

/**
 * Эта функция выполняет все необходимые действия для редактирования публикации: обновляет саму публикацию,
 * отвязывает старые (убранные из списка) и привязывает новые (добавленные в список) теги и права для просмотра.
 *
 * Возвращает сообщение об успешности выполнения редактирования.
 */
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

/**
 * Эта функция обновляет публикацию в базе данных, не затрагивая никакие связанные сущности.
 *
 * Возвращает успешность операции (true или false).
 */
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

/**
 * Эта функция удаляет публикацию по её ID.
 * Связки публикации с тегами и правами будут удалены автоматически на стороне базы данных.
 */
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

/**
 * Эта функция возвращает список самых популярных публикаций по их просмотрам.
 * Принимает на вход требуемое количество возвращённых публикаций (по умолчанию - 5).
 */
// TODO: order by comments count if number of views is equal
function get_most_popular(int $limit = 5): array
{
    $query = get_connection()->prepare('SELECT blog_entry.*, COUNT(entry_id) AS views_count from blog_entry 
        INNER JOIN entry_view ev on blog_entry.id = ev.entry_id 
                                                    GROUP BY entry_id ORDER BY views_count DESC LIMIT ?');
    $query->bind_param('i', $limit);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция возвращает список публикаций от авторов, на которых подписан пользователь.
 * Принимает на вход ID пользователя, параметры сортировки (по умолчанию - самые новые публикации в начале списка)
 * и ограничение по количеству возвращаемых публикаций.
 * Обязательным параметром является только ID пользователя.
 */
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