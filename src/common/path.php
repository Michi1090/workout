<?php

// 相対パスの指定
function currentIndex()
{
    return ['root' => './', 'users' => 'users/', 'index' => 'set'];
}

function currentRoot()
{
    return ['root' => './', 'users' => 'users/'];
}

function currentUsers()
{
    return ['root' => '../', 'users' => './'];
}
