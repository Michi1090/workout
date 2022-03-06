<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $id = $_GET['id'];
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $id = $_POST['id'];

    // 対象のレコードを削除
    $sql = 'DELETE FROM weight_logs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $stmt->execute();

    // インデックスページへリダイレクト
    header('Location: index.php');
    exit;
}

// ヘッダーのパス指定
$path_logs = './';
$path_users = '../users/';
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('../header.php') ?>

    <h2>トレーニングログ削除</h2>
    <p>選択したログを削除します。よろしいですか？</p>

    <!-- 削除フォーム -->
    <form method="post">
        <input type="hidden" name="id" value="<?= escape($id) ?>">
        <input type="submit" value="削除">
    </form>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
