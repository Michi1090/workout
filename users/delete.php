<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $id = $_SESSION['id'];
} else {
    // ログインしていないとき、ログインページへリダイレクト
    header('Location: login.php');
    exit;
}

// フォームから値が入力された場合、パスワードの判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $pass = $_POST['pass'];

    // ログインユーザーのパスワードを取得
    $sql = 'SELECT password FROM users WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($pass, $result['password'])) {
        // パスワードが一致する場合、ユーザー登録と対象ユーザーに紐づくログを削除
        $sql = 'DELETE users, weight_logs FROM users LEFT JOIN weight_logs ON users.id = user_id WHERE users.id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // 画面遷移フラグを設定して、完了画面へリダイレクト
        $_SESSION['flag'] = true;
        header('Location: delete_complete.php');
        exit;
    } else {
        // 入力されたパスワードが一致しない場合
        $error = '※パスワードが違います';
    }
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
                        <h1 class="text-center my-2">ユーザー登録削除</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <p class="mb-2">ユーザー登録を削除するには、パスワードを入力して「削除」ボタンを押してください</p>
                        <!-- 入力フォーム -->
                        <form method="post" onsubmit="return false;">
                            <div class="mb-4">
                                <label class="col-form-label" for="pass">パスワード</label>
                                <input class="form-control" type="password" name="pass" id="pass" required>
                                <p class="text-danger small mb-0"><?= isset($error) ? escape($error) : '' ?></p>
                            </div>
                            <div class="mb-3 d-grid gap-3">
                                <a class="btn btn-secondary" href="my_page.php">戻る</a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-modal" type="button">削除</button>
                            </div>
                            <!-- 確認用モーダル -->
                            <?php require_once('delete_confirm_modal.php') ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
