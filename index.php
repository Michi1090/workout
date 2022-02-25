<!--  ロジック
================================================================================================  -->
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


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('header.php') ?>

    <h2>インデックスページ</h2>
    <p><?= escape($message); ?></p>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
