<!--  ロジック
================================================================================================  -->
<?php

require_once('../common/db_connect.php');
require_once('../common/sanitize.php');
require_once('../common/path.php');

// セッションの開始
session_start();

// ログイン状態のとき、インデックスページへリダイレクトする
if (isset($_SESSION['id'])) {
	header('Location: ../index.php');
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
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!empty($result)) {
		// レコード取得に成功（ユーザー登録あり）した場合、パスワードチェックを行う
		if (password_verify($pass, $result['password'])) {
			// セッションIDを新しく生成（セッションハイジャック対策）
			session_regenerate_id(true);

			// パスワードが一致する場合、ログイン処理を行う
			$_SESSION['id'] = $result['id'];
			$_SESSION['name'] = $result['name'];

			// インデックスページへリダイレクト
			header('Location: ../index.php');
			exit;
		} else {
			// パスワードが一致しない場合
			$error = '※ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先にユーザー登録をしてください。';
		}
	} else {
		// レコード取得に失敗（ユーザー登録なし）した場合
		$error = '※ユーザー名、またはパスワードが違います。ユーザー登録をされていない方は先に新規登録をしてください。';
	}
}

// ヘッダーのパス指定
$path = currentUsers();
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../common/head.php') ?>

<body>
	<!-- header 読み込み -->
	<?php require_once('../common/header.php') ?>

	<main>
		<div class="container">
			<div class="justify-content-center">
				<!-- カード -->
				<div class="card">
					<!-- カードヘッダー -->
					<div class="card-header">
						<h1 class="text-center my-2">ログイン</h1>
					</div>
					<!-- カードボディ -->
					<div class="card-body">
						<p class="mb-2">ユーザー名とパスワードを入力してください</p>
						<p class="text-danger small mb-3"><?= isset($error) ? escape($error) : '' ?></p>
						
						<!-- 入力フォーム -->
						<form method="post">
							<div class="mb-3">
								<label class="form-label" for="name">ユーザー名</label>
								<input class="form-control" type="text" name="name" id="name" required>
							</div>
							<div class="mb-4">
								<label class="form-label" for="pass">パスワード</label>
								<input class="form-control" type="password" name="pass" id="pass" required>
							</div>
							<div class="mb-3 d-grid">
								<button class="btn btn-warning" type="submit">ログイン</button>
							</div>
						</form>

						<!-- ゲストログイン用フォーム -->
						<form method="post">
							<input type="hidden" name="name" value="guest">
							<input type="hidden" name="pass" value="123456Aa">
							<div class="mb-3 d-grid">
								<button class="btn btn-success" type="submit">ゲストログイン</button>
							</div>
						</form>

						<div class="text-center">
							<a href="sign_up.php">新規ユーザー登録はこちらから</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="../js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
</body>

</html>
