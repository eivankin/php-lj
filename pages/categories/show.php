<?php
if (!isset($id))
    $id = '';

header('Location: /entries/?category=' . $id);
exit();