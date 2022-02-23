<?php

// DB、及びセッション接続
require_once('db_connect.php');
session_start();
session_regenerate_id();

// ログイン済みの場合、マイページへリダイレクト
if (isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // HTMLのエスケープ処理
    require_once('sanitize.php');
    $post = escapeHtml($_POST);
    $name = $post['name'];
    $pass = $post['pass'];
    $pass_check = $post['pass_check'];

    /* バリデーション */
    // 入力されたユーザー名に一致するレコード数を取得
    $sql = 'SELECT COUNT(*) FROM users WHERE name = :name';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();

    // 入力されたユーザー名がテーブルに存在するかチェック
    if ($result['COUNT(*)'] == 1) {
        $errors['name'] = '※このユーザー名は既に使用されています。';
    }

    // 入力されたユーザー名が形式通りかチェック
    if (!preg_match('/^[a-zA-Z0-9]{1,30}$/', $name)) {
        $errors['name'] = '※ユーザー名は半角英数字30文字以内で入力してください';
    }

    // 入力されたパスワードが形式通りかチェック
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/', $pass)) {
        $errors['pass'] = '※パスワードは半角英数字8文字以上で、英大文字、英子文字、数字を最低1個以上含む必要があります';
    }

    // 確認用パスワードが一致するかチェック
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

        // 登録に引き続き、ログイン処理を行う
        $sql = 'SELECT * FROM users WHERE name = :name and password = :pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        $_SESSION['id'] = $result['id'];
        $_SESSION['name'] = $result['name'];
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Workout</title>
</head>

<body>
    <!-- ヘッダー -->
    <?php require_once('header.php') ?>

    <h2>新規登録ページ</h2>
    <p>任意のユーザー名とパスワードを入力してください</p>
    <form method="post">
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" required>
            <p style="color: red;"><?= isset($errors['name']) ? $errors['name'] : '' ?></p>
        </div>
        <div>
            <label>パスワード</label>
            <input type="password" name="pass" required>
            <p style="color: red;"><?= isset($errors['pass']) ? $errors['pass'] : '' ?></p>
        </div>
        <div>
            <label>パスワード（確認用）</label>
            <input type="password" name="pass_check" required>
            <p style="color: red;"><?= isset($errors['pass_check']) ? $errors['pass_check'] : '' ?></p>
        </div>
        <input type="submit" value="登録">
    </form>

</body>

</html>
