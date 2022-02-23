<?php

// DB、及びセッション接続
require_once('db_connect.php');
session_start();
session_regenerate_id();

// ログインしていないとき、ログインページへリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// GETでアクセスしたときの初期メッセージ
$message = '現在のパスワードと新しいパスワードを入力してください';

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // HTMLのエスケープ処理
    require_once('sanitize.php');
    $post = escapeHtml($_POST);
    $pass = $post['pass'];
    $pass_new = $post['pass_new'];
    $pass_check = $post['pass_check'];

    /* バリデーション */
    // ログインユーザーのパスワードを取得
    $sql = 'SELECT password FROM users WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

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
        $stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':pass', $hash_pass, PDO::PARAM_STR);
        $stmt->execute();

        $message = 'パスワードが変更されました';
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

    <h2>パスワード変更ページ</h2>
    <p><?= $message ?></p>
    <form method="post">
        <div>
            <label>現在のパスワード</label>
            <input type="password" name="pass" required>
            <p style="color: red;"><?= isset($errors['pass']) ? $errors['pass'] : '' ?></p>
        </div>
        <div>
            <label>新しいパスワード</label>
            <input type="password" name="pass_new" required>
            <p style="color: red;"><?= isset($errors['pass_new']) ? $errors['pass_new'] : '' ?></p>
        </div>
        <div>
            <label>新しいパスワード（確認用）</label>
            <input type="password" name="pass_check" required>
            <p style="color: red;"><?= isset($errors['pass_check']) ? $errors['pass_check'] : '' ?></p>
        </div>
        <input type="submit" value="変更">
    </form>

</body>

</html>
