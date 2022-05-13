<?php

if (!isset($id))
    $id = '';

header('Location: /entries/?tags[]=' . $id);
exit();
