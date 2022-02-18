<?php

session_start();

// ログイン状態のとき、インデックスページへリダイレクトする
if (isset($_SESSION['id'])) {
	header('Location:index.php');
	exit;
}

$message = 'ユーザー名とパスワードを入力してください';

// フォームから値が入力された場合、ログイン判定を行う
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = $_POST['name'];
	$pass = $_POST['pass'];

	try {
		// ユーザー名とパスワードが合致するレコードを取得
		$dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';
		$pdo = new PDO($dsn, 'root', '');
		$sql = 'SELECT * FROM users WHERE name = :name and password = :pass';
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':pass', $pass);
		$stmt->execute();
		$result = $stmt->fetch();

		if ($result != false) {
			// 対象のレコードが存在する（$resultの戻り値が存在する）場合、ログイン処理を行う
			$_SESSION['id'] = $result['id'];
			$_SESSION['name'] = $result['name'];
			header('Location: index.php');
			exit;
		} else {
			// 対象のレコードが存在しない（$resultの戻り値がfalse）場合
			$message = 'ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
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

	<h2>ログイン</h2>
	<p><?= $message ?></p>
	<form method="post">
		<div>
			<label>ユーザー名</label>
			<input type="text" name="name">
		</div>
		<div>
			<label>パスワード</label>
			<input type="password" name="pass">
		</div>
		<input type="submit" value="ログイン">
	</form>

</body>

</html>
