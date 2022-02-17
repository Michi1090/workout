<?php

$dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';

session_start();

// GETでアクセスしたときの初期メッセージ
$message = 'ユーザー名とパスワードを入力してください';
$error_msg_name = '';
$error_msg_pass = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = $_POST['name'];
	$pass = $_POST['pass'];

	try {
		// ユーザー登録がある（対象のレコードがある）場合、ユーザー名とパスワードが合致するレコード数を取得
		$pdo = new PDO($dsn, 'root', '');
		$sql = 'SELECT COUNT(*) FROM users WHERE name = :name and password = :pass';
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':pass', $pass);
		$stmt->execute();
		$result = $stmt->fetch();

		if ($result['COUNT(*)'] == 1) {
			// ユーザー名とパスワードが一致する（対象のレコードが1つある）の場合、ログイン処理を行う
			$_SESSION['login_name'] = $name;
			header('Location: index.php');
			exit;
		} else {
			// ユーザー名とパスワードが一致しない（対象のレコードがない）の場合
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
