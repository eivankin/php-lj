<?php
/**
 * Страница показа категории, перенаправляющая на список публикаций из этой категории.
 */

if (!isset($id))
    $id = '';

header('Location: /entries/?category=' . $id);
exit();