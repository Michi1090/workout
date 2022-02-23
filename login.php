<?php

// DB、及びセッション接続
require_once('db_connect.php');
session_start();
session_regenerate_id();

// ログイン状態のとき、インデックスページへリダイレクトする
if (isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

// フォームから値が入力された場合、ログイン判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// HTMLのエスケープ処理
	require_once('sanitize.php');
	$post = escapeHtml($_POST);
	$name = $post['name'];
	$pass = $post['pass'];

	$sql = 'SELECT * FROM users WHERE name = :name';
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':name', $name, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetch();

	if (password_verify($pass, $result['password'])) {
		// パスワードが一致する場合、ログイン処理を行う
		$_SESSION['id'] = $result['id'];
		$_SESSION['name'] = $result['name'];
		header('Location: index.php');
		exit;
	} else {
		// パスワードが一致しない場合
		$error = '※ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
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

	<h2>ログイン</h2>
	<p>ユーザー名とパスワードを入力してください</p>
	<p style="color: red;"><?= isset($error) ? $error : '' ?></p>
	<form method="post">
		<div>
			<label>ユーザー名</label>
			<input type="text" name="name" required>
		</div>
		<div>
			<label>パスワード</label>
			<input type="password" name="pass" required>
		</div>
		<input type="submit" value="ログイン">
	</form>

</body>

</html>
