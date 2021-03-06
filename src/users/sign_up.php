<!--  ロジック
================================================================================================  -->
<?php

require_once('../common/db_connect.php');
require_once('../common/sanitize.php');
require_once('../common/path.php');

// セッションの開始
session_start();

// ログイン済みの場合、マイページへリダイレクト
if (isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $pass_check = $_POST['pass_check'];

    // 入力されたユーザー名に一致するレコード数を取得
    $sql = <<<EOD
    SELECT COUNT(*)
    FROM users
    WHERE name = :name
    EOD;

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    /* バリデーション */
    // ユーザー名の重複
    if ($result['COUNT(*)'] == 1) {
        $errors['name'] = '※このユーザー名は既に使用されています';
    }
    // ユーザー名の形式
    if (!preg_match('/^[a-zA-Z0-9]{1,20}$/', $name)) {
        $errors['name'] = '※ユーザー名は半角英数字20文字以内で入力してください';
    }
    // パスワードの形式
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/', $pass)) {
        $errors['pass'] = '※パスワードは半角英数字8文字以上で英大文字、英子文字、数字を最低1個以上含む必要があります';
    }
    // 確認用パスワードとの一致
    if ($pass !== $pass_check) {
        $errors['pass_check'] = '※確認用パスワードが一致しません';
    }

    // バリデーションクリア（エラーメッセージなし）の場合
    if (empty($errors)) {
        // パスワードの暗号化
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

        // ユーザー登録処理
        $sql = <<<EOD
        INSERT INTO users
        SET name = :name, password = :pass
        EOD;

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();

        // 登録に引き続き、ログイン処理を行う
        $sql = <<<EOD
        SELECT *
        FROM users
        WHERE name = :name and password = :pass
        EOD;

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // セッションIDを新しく生成（セッションハイジャック対策）
        session_regenerate_id(true);

        // ログイン処理
        $_SESSION['id'] = $result['id'];
        $_SESSION['name'] = $result['name'];

        // インデックスページへリダイレクト
        header('Location: ../index.php');
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
    <?php require_once('../common/header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">
                <!-- カード -->
                <div class="card">
                    <!-- カードヘッダー -->
                    <div class="card-header">
                        <h1 class="text-center my-2">ユーザー登録</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <p class="mb-3">任意のユーザー名とパスワードを登録してください</p>
                        <!-- 入力フォーム -->
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label" for="name">ユーザー名</label>
                                <input class="form-control" type="text" name="name" id="name" required>
                                <p class="text-danger small mb-0"><?= isset($errors['name']) ? escape($errors['name']) : '' ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="pass">パスワード</label>
                                <input class="form-control" type="password" name="pass" id="pass" required>
                                <p class="text-danger small mb-0"><?= isset($errors['pass']) ? escape($errors['pass']) : '' ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="pass_check">パスワード（確認用）</label>
                                <input class="form-control" type="password" name="pass_check" id="pass_check" required>
                                <p class="text-danger small mb-0"><?= isset($errors['pass_check']) ? escape($errors['pass_check']) : '' ?></p>
                            </div>
                            <div class="mb-3 d-grid">
                                <button class="btn btn-warning" type="submit">登録</button>
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
