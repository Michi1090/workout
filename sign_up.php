<?php

$dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';

session_start();

// GETでアクセスしたときの初期メッセージ
$message = '任意のユーザー名とパスワードを入力してください';
$error_msg_name = '';
$error_msg_pass = '';
$error_msg_pass_check = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $pass_check = $_POST['pass_check'];

    try {
        // name = :name のレコード数を取得
        $pdo = new PDO($dsn, 'root', '');
        $sql = 'SELECT COUNT(*) FROM users WHERE name = :name';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['COUNT(*)'] != 0) {
            // 入力されたユーザー名がテーブルに存在する（登録済み）の場合
            $error_msg_name = 'このユーザー名は既に使用されています。';
        }

        if ($pass !== $pass_check) {
            // 入力されたパスワードが確認用と一致しない場合
            $error_msg_pass_check = '確認用パスワードが一致しません';
        }

        if ($result['COUNT(*)'] == 0 && $pass === $pass_check) {
            // ユーザー名がテーブルに存在しない（未登録）、尚且つパスワードが確認用と一致する場合
            $pdo = new PDO($dsn, 'root', '');
            $sql = 'INSERT INTO users SET name = :name, password = :pass';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':pass', $pass);
            $stmt->execute();

            // 登録に引き続き、ログイン処理を行う
            $_SESSION['login_name'] = $name;
            header('Location: index.php');
            exit;
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

    <h2>ユーザー登録ページ</h2>
    <p><?= $message ?></p>
    <form method="post">
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name">
            <p><?= $error_msg_name ?></p>
        </div>
        <div>
            <label>パスワード</label>
            <input type="password" name="pass">
            <p><?= $error_msg_pass ?></p>
        </div>
        <div>
            <label>パスワード（確認用）</label>
            <input type="password" name="pass_check">
            <p><?= $error_msg_pass_check ?></p>
        </div>
        <input type="submit" value="登録">
    </form>


</body>

</html>
