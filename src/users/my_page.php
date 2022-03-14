<!--  ロジック
================================================================================================  -->
<?php

require_once('../common/sanitize.php');
require_once('../common/path.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき、メッセージを表示
    $name = $_SESSION['name'];
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: login.php');
    exit;
}

// ヘッダーのパス指定
$path = currentUsers();

?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../common//head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('../common/header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">
                <!-- カード -->
                <div class="card">
                    <!-- カードヘッダー -->
                    <div class="card-header">
                        <h1 class="text-center my-2">マイページ</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <div class="mb-4">
                            <h2 class="fs-5 mb-3"><i class="fas fa-user me-2"></i>ユーザー名 : <?= escape($name) ?></h2>
                            <p class="mb-0">こちらのページからパスワードの変更とユーザー登録の削除が行えます。</p>
                        </div>
                        <div class="d-grid gap-3">
                            <a class="btn btn-warning" href="../index.php">トレーニングログ一覧</a>
                            <a class="btn btn-success" href="change_password.php">パスワード変更</a>
                            <a class="btn btn-danger" href="delete.php">ユーザー登録削除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
</body>

</html>
