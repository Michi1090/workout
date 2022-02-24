<?php

require_once('db_connect.php');
require_once('sanitize.php');

// セッションの開始
session_start();
session_regenerate_id();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $message = $_SESSION['name'] . ' の筋トレログ一覧';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Workout</title>
</head>

<body>
    <!-- ヘッダー -->
    <?php require_once('header.php') ?>

    <h2>インデックスページ</h2>
    <p><?= escape($message); ?></p>

</body>

</html>
