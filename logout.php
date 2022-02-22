<?php

// セッションを破棄してログインページへリダイレクト
session_start();
session_regenerate_id();
$_SESSION = array();
session_destroy();
header('Location: login.php');
exit;
