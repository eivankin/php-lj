<?php
require_once 'db/blog_entry/util.php';
$title = 'Публикации';
$content = '
<a href="./new">Добавить публикацию</a>
';

//foreach (get_all_entries() as $entry)
//    $content .= json_encode($entry) . '<br>';
require_once 'pages/base.php';
