<?php

$dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';

session_start();
$message = 'ユーザー名とパスワードを入力してください';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = $_POST['name'];
	$pass = $_POST['pass'];

	try {
		// name = :name のレコード数を取得
		$pdo = new PDO($dsn, 'root', '');
		$sql = 'SELECT COUNT(*) FROM users WHERE name = :name';
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':name', $name);
		$stmt->execute();
		$result = $stmt->fetch();

		if ($result['COUNT(*)'] == 0) {
			// ユーザー未登録（対象のレコードが0）の場合
			$message = 'ユーザー名が違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
		} else {
			// ユーザー登録がある（対象のレコードがある）場合、ユーザー名とパスワードが合致するレコード数を取得
			$sql = 'SELECT COUNT(*) FROM users WHERE name = :name and password = :pass';
			$stmt = $pdo->prepare($sql);
			$stmt->bindValue(':name', $name);
			$stmt->bindValue(':pass', $pass);
			$stmt->execute();
			$result = $stmt->fetch();

			if ($result['COUNT(*)'] == 1) {
				// ユーザー名とパスワードが一致する（対象のレコードが1つある）の場合、ログイン処理
				$_SESSION['login_name'] = $name;
				// $sql = 'SELECT id FROM users WHERE name = :name and password = :pass';
				// $stmt = $pdo->prepare($sql);
				// $stmt->bindValue(':name', $name);
				// $stmt->bindValue(':pass', $pass);
				// $result = $stmt->fetch();
				// $_SESSION['login_id'] = $result['id'];
				header('Location: index.php');
				exit;
			} else {
				// ユーザー名とパスワードが一致しない（対象のレコードがない）の場合
				$message = 'パスワードが違います';
			}
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
	<h1>ログイン</h1>
	<p><?= $message ?></p>
	<table>
		<form method="post">
			<tr>
				<th><label>USERNAME : </label></th>
				<td><input type="text" name="name"></td>
			</tr>
			<tr>
				<th><label>PASSWORD : </label></th>
				<td><input type="password" name="pass"></td>
			</tr>
			<tr>
				<th></th>
				<td><input type="submit" value="ログイン"></td>
			</tr>

		</form>
	</table>

	<!-- ヘッダーが完成するまでの臨時 -->
	<a href="logout.php">ログアウト</a>

</body>

</html>
