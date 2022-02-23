<?php

// セッション接続
session_start();
session_regenerate_id();

// 削除ページ以外からアクセスしたとき、インデックスページへリダイレクト
if ($_SESSION['flag'] !== true) {
    $_SESSION = array();
    session_destroy();
    header('Location: index.php');
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

    <h2>ユーザー登録 削除完了</h2>
    <p>ユーザー登録を削除しました。</br>ご利用ありがとうございました。</p>
    <a href="sign_up.php">新規登録ページ</a>
</body>

</html>
