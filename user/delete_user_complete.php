<!--  ロジック
================================================================================================  -->
<?php

// セッション接続
session_start();

if ($_SESSION['flag']) {
    // フラグtrue（削除ページからアクセスした）の場合、フラグ削除
    $_SESSION = array();
    session_destroy();
} else {
    // フラグがない（削除ページ以外からアクセスした）場合、インデックスページへリダイレクト
    header('Location: ../log/index.php');
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

    <h2>ユーザー登録 削除完了</h2>
    <p>ユーザー登録を削除しました。</br>ご利用ありがとうございました。</p>
    <a href="sign_up.php">新規登録ページ</a>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
