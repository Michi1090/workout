<?php

session_start();

// ログインしていないとき、ログインページへリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location:login.php');
    exit;
}

// GETでアクセスしたときの初期メッセージ
$message = '現在のパスワードと新しいパスワードを入力してください';
$error_msg_pass = '';
$error_msg_pass_new = '';
$error_msg_pass_check = '';

// フォームから値が入力された場合、パスワードの判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = $_POST['pass'];
    $pass_new = $_POST['pass_new'];
    $pass_check = $_POST['pass_check'];

    try {
        // 現在のパスワードが正しいかを判定
        $dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';
        $pdo = new PDO($dsn, 'root', '');
		$sql = 'SELECT COUNT(*) FROM users WHERE id = :id and password = :pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['id']);
        $stmt->bindValue(':pass', $pass);
        $stmt->execute();
        $result = $stmt->fetch();

        // 入力されたパスワードが一致しない場合
        if ($result['COUNT(*)'] == 0) {
            $error_msg_pass = 'パスワードが違います。';
        }

        // 入力されたパスワードが確認用と一致しない場合
        if ($pass_new !== $pass_check) {
            $error_msg_pass_check = '確認用パスワードが一致しません';
        }

        // パスワードの判定がすべてOKの場合
        if ($result['COUNT(*)'] == 1 && $pass_new === $pass_check) {
            // パスワードの更新を行う
            $sql = 'UPDATE users SET password = :pass WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $_SESSION['id']);
            $stmt->bindValue(':pass', $pass_new);
            $stmt->execute();

            $message = 'パスワードが変更されました';
        }
    } catch (PDOException $e) {
        // DBアクセスに失敗した場合、エラーメッセージを表示
        $message = $e->getMessage() . '<br/>時間をおいてから再度お試しください。';
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
            <input type="password" name="pass">
            <p><?= $error_msg_pass ?></p>
        </div>
        <div>
            <label>新しいパスワード</label>
            <input type="password" name="pass_new">
            <p><?= $error_msg_pass_new ?></p>
        </div>
        <div>
            <label>新しいパスワード（確認用）</label>
            <input type="password" name="pass_check">
            <p><?= $error_msg_pass_check ?></p>
        </div>
        <input type="submit" value="変更">
    </form>


</body>

</html>
