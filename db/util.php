<?php
function make_order_query(string $order_by_column = null, bool $order_desc = true,
                          array  $cols = ['id', 'published', 'edited', 'title']): string
{
    $order = '';
    if (!empty($order_by_column) && in_array($order_by_column, $cols))
        $order = " ORDER BY {$order_by_column} " . (($order_desc) ? 'DESC' : 'ASC');
    return $order;
}

function make_limit_query(int $limit = null): string
{
    $limit_query = '';
    if (isset($limit))
        $limit_query = " LIMIT {$limit}";
    return $limit_query;
}