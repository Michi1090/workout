<?php

// セッションを破棄してログインページへリダイレクト
session_start();
$_SESSION = array();
session_destroy();
header('Location: login.php');
exit;
