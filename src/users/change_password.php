<!--  ロジック
================================================================================================  -->
<?php

require_once('../common//db_connect.php');
require_once('../common//sanitize.php');
require_once('../common/path.php');

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

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $pass = $_POST['pass'];
    $pass_new = $_POST['pass_new'];
    $pass_check = $_POST['pass_check'];

    // ログインユーザーのパスワードを取得
    $sql = 'SELECT password FROM users WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    /* バリデーション */
    // パスワードが一致するかチェック
    if (!password_verify($pass, $result['password'])) {
        $errors['pass'] = '※パスワードが違います';
    }

    // 新パスワードが形式通りかチェック
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/', $pass_new)) {
        $errors['pass_new'] = '※パスワードは半角英数字8文字以上で、英大文字、英子文字、数字を最低1個以上含む必要があります';
    }

    // 確認用パスワードが一致するかチェック
    if ($pass_new !== $pass_check) {
        $errors['pass_check'] = '※確認用パスワードが一致しません';
    }

    // バリデーションクリア（エラーメッセージなし）の場合
    if (empty($errors)) {
        // パスワードの暗号化
        $hash_pass = password_hash($pass_new, PASSWORD_DEFAULT);

        // パスワードの更新処理を行う
        $sql = 'UPDATE users SET password = :pass WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();

        // マイページへリダイレクト
        header('Location: my_page.php');
        exit;
    }
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
    <?php require_once('../common//header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">
                <!-- カード -->
                <div class="card">
                    <!-- カードヘッダー -->
                    <div class="card-header">
                        <h1 class="text-center my-2">パスワード変更</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <p class="mb-3">現在のパスワードと新しいパスワードを入力してください</p>
                        <!-- 入力フォーム -->
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label" for="name">現在のパスワード</label>
                                <input class="form-control" type="password" name="pass" id="pass" required>
                                <p class="text-danger small mb-0"><?= isset($errors['pass']) ? escape($errors['pass']) : '' ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="pass">新しいパスワード</label>
                                <input class="form-control" type="password" name="pass_new" id="pass_new" required>
                                <p class="text-danger small mb-0"><?= isset($errors['pass_new']) ? escape($errors['pass_new']) : '' ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="pass_check">パスワード（確認用）</label>
                                <input class="form-control" type="password" name="pass_check" id="pass_check" required>
                                <p class="text-danger small mb-0"><?= isset($errors['pass_check']) ? escape($errors['pass_check']) : '' ?></p>
                            </div>
                            <div class="mb-3 d-grid gap-3">
                                <a class="btn btn-secondary" href="my_page.php">戻る</a>
                                <button class="btn btn-warning" type="submit">更新</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
</body>

</html>
