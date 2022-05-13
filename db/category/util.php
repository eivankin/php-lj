<?php

require_once 'db/connection.php';
require_once 'db/util.php';

/**
 * Эта функция возвращает список из всех категорий, отсортированный согласно переданным параметрам.
 *
 * Возвращает ассоциативный массив из категорий.
 */
function get_categories(string $order_by_column = null, bool $order_desc = true): array
{
    $order = make_order_query($order_by_column, $order_desc, ['id', 'name', 'parent_id']);
    return get_connection()->query("SELECT * FROM category{$order}")->fetch_all(MYSQLI_ASSOC);
}

/**
 * Эта функция создаёт категорию в базе данных.
 * Принимает на вход название и ID родительской категории.
 *
 * Категория может не иметь родителя, в таком случае она является корневой.
 */
function create_category(string $name, int $parent_id = null)
{
    get_connection()->begin_transaction();
    if (isset($parent_id) && $parent_id != 0) {
        $query = get_connection()->prepare('INSERT INTO category(name, parent_id) VALUES (?, ?)');
        $query->bind_param('si', $name, $parent_id);
    } else {
        $query = get_connection()->prepare('INSERT INTO category(name, parent_id) VALUES (?, NULL)');
        $query->bind_param('s', $name);
    }
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция обновляет категорию в базе данных.
 * Принимает на вход ID текущей категории, название и ID родительской категории.
 */
function update_category(int $id, string $name, int $parent_id)
{
    get_connection()->begin_transaction();
    $query = get_connection()->prepare('UPDATE category SET name = ?, parent_id = ? WHERE id = ?');
    $query->bind_param('ssi', $name, $parent_id, $id);
    $query->execute();
    get_connection()->commit();
}

/**
 * Эта функция удаляет категорию по её ID.
 * Связанные с категорией публикации и дочерние категории будут удалены автоматически на стороне базы данных.
 *
 * Возвращает успешность выполнения удаления (true или false).
 */
function delete_category(int $id): bool
{
    try {
        get_connection()->begin_transaction();
        $query = get_connection()->prepare('DELETE FROM category WHERE id = ?');
        $query->bind_param('i', $id);
        $result = $query->execute();
        get_connection()->commit();
        return $result;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 * Эта функция возвращает категорию по её ID.
 *
 * Если категория не найдена, возвращает null.
 */
function get_category(int $id)
{
    $query = get_connection()->prepare('SELECT * FROM category WHERE id = ?');
    $query->bind_param('i', $id);
    $query->execute();

    return $query->get_result()->fetch_assoc();
}
