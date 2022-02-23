<?php

// セッション接続
session_start();
session_regenerate_id();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき、メッセージを表示
    $message = 'ようこそ、' . $_SESSION['name'] . 'さん！</br>こちらのページからパスワードの変更とユーザー登録の削除を行えます。';
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

    <h2>マイページ</h2>
    <p><?= $message ?></p>
    <div>
        <a href="change_password.php">パスワード変更</a>
        <a href="delete_user.php">ユーザー登録削除</a>
    </div>

</body>

</html>
