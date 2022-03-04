<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

// ログイン状態のとき、インデックスページへリダイレクトする
if (isset($_SESSION['id'])) {
	header('Location: ../logs/index.php');
	exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// フォームの入力値を代入
	$name = $_POST['name'];
	$pass = $_POST['pass'];

	// ユーザー名に合致するレコードを取得
	$sql = 'SELECT * FROM users WHERE name = :name';
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':name', $name, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetch();

	if ($result !== false) {
		// レコード取得に成功（ユーザー登録あり）した場合、パスワードチェックを行う
		if (password_verify($pass, $result['password'])) {
			// パスワードが一致する場合、ログイン処理を行う
			$_SESSION['id'] = $result['id'];
			$_SESSION['name'] = $result['name'];

			// セッションIDを新しく生成（セッションハイジャック対策）
			session_regenerate_id();

			// インデックスページへリダイレクト
			header('Location: ../logs/index.php');
			exit;
		} else {
			// パスワードが一致しない場合
			$error = '※ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
		}
	} else {
		// レコード取得に失敗（ユーザー登録なし）した場合
		$error = '※ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
	}
}

// ヘッダーのパス指定
$path_logs = '../logs/';
$path_users = './';
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../head.php') ?>

<body>
	<!-- header 読み込み -->
	<?php require_once('../header.php') ?>

	<h2>ログイン</h2>
	<p>ユーザー名とパスワードを入力してください</p>
	<p style="color: red;"><?= isset($error) ? escape($error) : '' ?></p>
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

	<script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
