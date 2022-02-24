<?php

require_once('db_connect.php');
require_once('sanitize.php');

// セッションの開始
session_start();
session_regenerate_id();

// ログインしていないとき、ログインページへリダイレクト
if (!isset($_SESSION['id'])) {
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
    $stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();

    // パスワードが一致するかチェック
    if (password_verify($pass, $result['password'])) {
        $id = $_SESSION['id'];
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // 画面遷移フラグを設定して、完了画面へリダイレクト
        $_SESSION['flag'] = true;
        header('Location: delete_user_complete.php');
        exit;
    } else {
        // 入力されたパスワードが一致しない場合
        $error = '※パスワードが違います';
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
    <p style="color :red">※一度ユーザー登録を削除すると、すべての筋トレログが削除され元に戻せません。</p>
    <p>ユーザー登録を削除するには、パスワードを入力して「確認」ボタンを押してください</p>
    <form method="post">
        <div>
            <label>パスワード</label>
            <input type="password" name="pass" required>
            <p style="color: red;"><?= isset($error) ? escape($error) : '' ?></p>
        </div>
        <input type="button" value="戻る" onclick="history.back(-1)">
        <input type="submit" value="確認">
    </form>


</body>

</html>
