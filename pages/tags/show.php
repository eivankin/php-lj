<?php
/**
 * Страница показа тега, перенаправляющая на список публикаций с этим тегом.
 */

if (!isset($id))
    $id = '';

header('Location: /entries/?tags[]=' . $id);
exit();
