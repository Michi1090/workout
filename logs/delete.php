<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $id = $_GET['id'];
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// 対象の筋トレログを取得
$sql = 'SELECT date, part, machine, weight, time, set_count, work_load, note FROM weight_logs WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetch(PDO::FETCH_ASSOC);

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $id = $_POST['id'];

    // 対象のレコードを削除
    $sql = 'DELETE FROM weight_logs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    $stmt->execute();

    // インデックスページへリダイレクト
    header('Location: index.php');
    exit;
}

// ヘッダーのパス指定
$path_logs = './';
$path_users = '../users/';
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('../head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('../header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">

                <!-- 対象のログidが存在する場合  -->
                <?php if (!empty($logs)) : ?>
                    <!-- カード -->
                    <div class="card">
                        <!-- カードヘッダー -->
                        <div class="card-header">
                            <h1 class="text-center my-2">トレーニングログ削除</h1>
                        </div>
                        <!-- カードボディ -->
                        <div class="card-body">
                            <p class="mb-2">このトレーニングログを削除します。よろしいですか？</p>
                            <!-- 削除内容 -->
                            <div class="border-top border-bottom py-2">
                                <p class="mb-2">日付 : <?= escape($logs['date']) ?></p>
                                <p class="mb-2">部位 : <?= escape($logs['part']) ?></p>
                                <p class="mb-2">マシン : <?= escape($logs['machine']) ?></p>
                                <p class="mb-2">重量 : <?= escape($logs['weight']) ?> kg</p>
                                <p class="mb-2">回数 : <?= escape($logs['time']) ?> 回</p>
                                <p class="mb-2">セット : <?= escape($logs['set_count']) ?> set</p>
                                <p class="mb-2">負荷 : <?= escape($logs['work_load']) ?></p>
                                <p class="mb-0">メモ : <?= escape($logs['note']) ?></p>
                            </div>

                            <!-- 削除フォーム -->
                            <form class="mt-3" method="post">
                                <input type="hidden" name="id" value="<?= escape($id) ?>">
                                <div class="d-grid gap-3">
                                    <a class="btn btn-secondary" href="index.php">戻る</a>
                                    <button class="btn btn-danger" type="submit">削除</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 該当ログなしの場合  -->
                <?php else : ?>
                    <h6>※該当するトレーニングログが存在しません</h6>
                <?php endif ?>
            </div>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
</body>

</html>
