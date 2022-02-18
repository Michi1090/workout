<?php

session_start();

// ログインしていないとき、ログインページへリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location:login.php');
    exit;
}

// GETでアクセスしたときの初期メッセージ
$message = '一度ユーザー登録を削除すると、すべての筋トレログが削除され元に戻せません。</br>本当にユーザー登録を削除しますか？';

// フォームから値が入力された場合、パスワードの判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    try {
        // usersテーブルからログイン中のユーザーを削除
        $dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';
        $pdo = new PDO($dsn, 'root', '');
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        // セッションを破棄して新規登録ページへリダイレクト
        $_SESSION = array();
        session_destroy();
        header('Location: sign_up.php');
        exit;
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

    <h2>ユーザー登録 確認ページ</h2>
    <p><?= $message ?></p>
    <form method="post">
        <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">
        <input type="submit" value="やっぱり止める" formaction="my_page.php">
        <input type="submit" value="削除する">
    </form>

</body>

</html>
