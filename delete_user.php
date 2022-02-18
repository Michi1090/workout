<?php

session_start();

// ログインしていないとき、ログインページへリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location:login.php');
    exit;
}

// GETでアクセスしたときの初期メッセージ
$message = 'ユーザー登録を削除するには、パスワードを入力して「確認」ボタンを押してください';

// フォームから値が入力された場合、パスワードの判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = $_POST['pass'];

    try {
        // 入力されたパスワードが正しいかを判定
        $dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';
        $pdo = new PDO($dsn, 'root', '');
        $sql = 'SELECT COUNT(*) FROM users WHERE id = :id and password = :pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['id']);
        $stmt->bindValue(':pass', $pass);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['COUNT(*)'] == 0) {
             // 入力されたパスワードが一致しない場合
            $message = 'パスワードが違います';
        } else {
             // 入力されたパスワードが一致する場合、確認画面へリダイレクト
            header('Location: delete_user_confirm.php');
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

    <h2>ユーザー登録削除ページ</h2>
    <p><?= $message ?></p>
    <form method="post">
        <div>
            <label>パスワード</label>
            <input type="password" name="pass">
        </div>
        <input type="submit" value="確認">
    </form>


</body>

</html>
