<!--  ロジック
================================================================================================  -->
<?php

require_once('common/db_connect.php');
require_once('common/sanitize.php');
require_once('common/select_box.php');
require_once('common/path.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $user_id = $_SESSION['id'];
    $message = '';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: users/login.php');
    exit;
}

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $date = $_POST['date'];
    $part = $_POST['part'];
    $machine = $_POST['machine'];
    $weight = $_POST['weight'];
    $time = $_POST['time'];
    $set_count = $_POST['set_count'];
    $work_load = $_POST['work_load'];
    $note = $_POST['note'];

    // バリデーション
    require_once('common/validation.php');

    // バリデーションクリア（エラーメッセージなし）の場合
    if (empty($errors)) {
        // 新規レコードを挿入
        $sql = <<<EOD
        INSERT INTO weight_logs (user_id, date, part, machine, weight, time, set_count, work_load, note)
        VALUES (:user_id, :date, :part, :machine, :weight, :time, :set_count, :work_load, :note)
        EOD;

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':part', $part, PDO::PARAM_STR);
        $stmt->bindValue(':machine', $machine, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $weight, PDO::PARAM_STR); // 小数に対応する型がないので、PARAM_STRで代用
        $stmt->bindValue(':time', $time, PDO::PARAM_INT);
        $stmt->bindValue(':set_count', $set_count, PDO::PARAM_INT);
        $stmt->bindValue(':work_load', $work_load, PDO::PARAM_STR);
        $stmt->bindValue(':note', $note, PDO::PARAM_STR);
        $stmt->execute();

        $message = 'トレーニングログを登録しました！';
    }
}

// ヘッダーのパス指定
$path = currentRoot();
?>


<!--  ビュー
================================================================================================  -->
<!-- head 読み込み -->
<?php require_once('common/head.php') ?>

<body>
    <!-- header 読み込み -->
    <?php require_once('common/header.php') ?>

    <main>
        <div class="container">
            <div class="justify-content-center">
                <!-- カード -->
                <div class="card mb-2">
                    <!-- カードヘッダー -->
                    <div class="card-header">
                        <h1 class="text-center my-2">トレーニングログ登録</h1>
                    </div>
                    <!-- カードボディ -->
                    <div class="card-body">
                        <p class="text-danger mb-3"><?= escape($message) ?></p>
                        <!-- 登録フォーム -->
                        <form method="post">
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="date">日付</label>
                                <div class="col-9"><input class="form-control" type="date" name="date" max="9999-12-31" id="date" required></div>
                                <p class="text-danger small mb-0"><?= isset($errors['date']) ? escape($errors['date']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="part">部位</label>
                                <div class="col-9">
                                    <select class="form-select" name="part" id="part" required>
                                        <?php foreach ($form_parts as $form_part) : ?>
                                            <option><?= escape($form_part) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <p class="text-danger small mb-0"><?= isset($errors['part']) ? escape($errors['part']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="machine">マシン</label>
                                <div class="col-9"><input class="form-control" type="text" name="machine" id="machine" maxlength="20" placeholder="トレーニングマシンを入力" required></div>
                                <p class="text-danger small mb-0"><?= isset($errors['machine']) ? escape($errors['machine']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="weight">重量</label>
                                <div class="col-7"><input class="form-control" type="number" name="weight" id="weight" step="0.1" min="0" max="999.9" placeholder="0.0"></div>
                                <label class="col-form-label col-2" for="weight">kg</label>
                                <p class="text-danger small mb-0"><?= isset($errors['weight']) ? escape($errors['weight']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="time">回数</label>
                                <div class="col-7"><input class="form-control" type="number" name="time" id="time" min="0" max="99" placeholder="0"></div>
                                <label class="col-form-label col-2" for="time">回</label>
                                <p class="text-danger small mb-0"><?= isset($errors['time']) ? escape($errors['time']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="set_count">セット</label>
                                <div class="col-7"><input class="form-control" type="number" name="set_count" id="set_count" min="0" max="99" placeholder="0"></div>
                                <label class="col-form-label col-2" for="set_count">set</label>
                                <p class="text-danger small mb-0"><?= isset($errors['set_count']) ? escape($errors['set_count']) : '' ?></p>
                            </div>
                            <div class="row mb-3">
                                <label class="col-form-label col-3" for="work_load">負荷</label>
                                <div class="col-9">
                                    <select class="form-select" name="work_load" id="work_load">
                                        <option value="">--</option>
                                        <?php foreach ($form_loads as $form_load) : ?>
                                            <option><?= escape($form_load) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <p class="text-danger small mb-0"><?= isset($errors['work_load']) ? escape($errors['work_load']) : '' ?></p>
                            </div>
                            <div class="row mb-4">
                                <label class="col-form-label col-3" for="note">メモ</label>
                                <div class="col-9"><input class="form-control" type="text" name="note" id="note" size="50" maxlength="30"></div>
                                <p class="text-danger small mb-0"><?= isset($errors['note']) ? escape($errors['note']) : '' ?></p>
                            </div>
                            <div class="d-grid gap-3">
                                <button class="btn btn-warning" type="submit">登録</button>
                                <a class="btn btn-secondary" href="index.php">戻る</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
    <script src="js/date.js"></script><!-- 日付のデフォルト値 -->
</body>

</html>
