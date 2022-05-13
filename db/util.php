<?php
/**
 * Эта функция убеждается в корректности аргументов для сортировки списка сущностей из базы данных и
 * создаёт корректную часть SQL-запроса для применения сортировки.
 * Иначе возвращается пустая строка.
 *
 * Принимает на вход параметры сортировки и список столбцов, по которым разрешена сортировка.
 */
function make_order_query(string $order_by_column = null, bool $order_desc = true,
                          array  $cols = ['id', 'published', 'edited', 'title']): string
{
    $order = '';
    if (!empty($order_by_column) && in_array($order_by_column, $cols))
        $order = " ORDER BY {$order_by_column} " . (($order_desc) ? 'DESC' : 'ASC');
    return $order;
}

/**
 * Эта функция убеждается в корректности аргументов для ограничения количества сущностей в ответе от базы данных создаёт
 * корректную часть SQL-запроса для применения ограничения.
 * Иначе возвращается пустая строка.
 *
 * Принимает на вход необязательный параметр, определяющий количество элементов в будущем запросе.
 */
function make_limit_query($limit = null): string
{
    $limit_query = '';
    if (isset($limit) && is_a($limit, 'int'))
        $limit_query = " LIMIT {$limit}";
    return $limit_query;
}