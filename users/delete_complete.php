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
    header('Location: ../logs/index.php');
    exit;
}

// ヘッダーのパス指定
$path_logs = '../logs/';
$path_users = './';
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('../header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">
                <!-- カード -->
                <div class="card">
                    <!-- カードヘッダー -->
                    <div class="card-header">
                        <h1 class="text-center my-2">ユーザー登録 削除完了</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <p class="my-2">ユーザー登録を削除しました。</br>ご利用ありがとうございました。</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
