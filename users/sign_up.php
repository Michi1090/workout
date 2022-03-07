<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

// ログイン済みの場合、マイページへリダイレクト
if (isset($_SESSION['id'])) {
    header('Location: ../logs/index.php');
    exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $pass_check = $_POST['pass_check'];

    // 入力されたユーザー名に一致するレコード数を取得
    $sql = 'SELECT COUNT(*) FROM users WHERE name = :name';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();

    /* バリデーション */
    // ユーザー名の重複
    if ($result['COUNT(*)'] == 1) {
        $errors['name'] = '※このユーザー名は既に使用されています。';
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

        // 登録処理を行う
        $sql = 'INSERT INTO users SET name = :name, password = :pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();

        // セッションIDを新しく生成（セッションハイジャック対策）
        session_regenerate_id(true);

        // 登録に引き続き、ログイン処理を行う
        $sql = 'SELECT * FROM users WHERE name = :name and password = :pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        $_SESSION['id'] = $result['id'];
        $_SESSION['name'] = $result['name'];

        // インデックスページへリダイレクト
        header('Location: ../logs/index.php');
        exit;
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

    <h2>新規登録ページ</h2>
    <p>任意のユーザー名とパスワードを入力してください</p>
    <form method="post">
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" required>
            <p style="color: red;"><?= isset($errors['name']) ? escape($errors['name']) : '' ?></p>
        </div>
        <div>
            <label>パスワード</label>
            <input type="password" name="pass" required>
            <p style="color: red;"><?= isset($errors['pass']) ? escape($errors['pass']) : '' ?></p>
        </div>
        <div>
            <label>パスワード（確認用）</label>
            <input type="password" name="pass_check" required>
            <p style="color: red;"><?= isset($errors['pass_check']) ? escape($errors['pass_check']) : '' ?></p>
        </div>
        <input type="submit" value="登録">
    </form>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
