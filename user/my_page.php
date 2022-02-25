<!--  ロジック
================================================================================================  -->
<?php

require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき、メッセージを表示
    $message = 'ようこそ、' . $_SESSION['name'] . 'さん！';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: login.php');
    exit;
}

// ヘッダーのパス指定
$path_log = '../log/';
$path_user = './';
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('../header.php') ?>

    <h2>マイページ</h2>
    <p><?= escape($message) ?></p>
    <p>こちらのページからパスワードの変更とユーザー登録の削除を行えます。</p>
    <div>
        <a href="change_password.php">パスワード変更</a>
        <a href="delete_user.php">ユーザー登録削除</a>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
