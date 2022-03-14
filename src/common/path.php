<?php

function currentIndex (){
    return ['root' => './', 'users' => 'users/', 'index' => 'set'];
}

function currentRoot (){
    return ['root' => './', 'users' => 'users/'];
}

function currentUsers (){
    return ['root' => '../', 'users' => './'];
}
