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
    $message = '';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// セレクトボックスの値
$form_parts = ['肩', '腕', '胸', '腹', '背中', '脚', 'その他'];
$form_loads = ['VERY EASY', 'EASY', 'NORMAL', 'HARD', 'VERY HARD'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') { // GETでアクセスした場合
    // // 入力フォームの初期値
    // $date = $part = $machine = '';

    // ユーザーの全筋トレログを取得
    $sql = 'SELECT date, part, machine, weight, time, set_count, work_load, note FROM weight_logs WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
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
    require_once('validation.php');

    // バリデーションクリア（エラーメッセージなし）の場合
    if (empty($errors)) {
        // 新規レコードを挿入
        $sql = 'INSERT INTO weight_logs (user_id, date, part, machine, weight, time, set_count, work_load, note) VALUES (:user_id, :date, :part, :machine, :weight, :time, :set_count, :work_load, :note)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
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

    <h2>新規トレーニングログ作成</h2>
    <p><?= escape($message) ?></p>

    <!-- セレクトボックス -->
    <form method="post">
        <div>
            <label for="date">日付</label>
            <input type="date" name="date" max="9999-12-31" id="date" value="<?= $result['date'] ?>" required>
            <p style="color: red;"><?= isset($errors['date']) ? escape($errors['date']) : '' ?></p>
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part" required>
                <?php foreach ($form_parts as $form_part) : ?>
                    <!-- 選択されたログの値と合致する場合、selected属性を付加する -->
                    <option value="<?= escape($form_part) ?>" <?= $form_part === $result['part'] ? 'selected' : '' ?>><?= escape($form_part) ?></option>
                <?php endforeach ?>
            </select>
            <p style="color: red;"><?= isset($errors['part']) ? escape($errors['part']) : '' ?></p>
        </div>
        <div>
            <label for="machine">マシン</label>
            <input type="text" name="machine" id="machine" value="<?= $result['machine'] ?>" required>
            <p style="color: red;"><?= isset($errors['machine']) ? escape($errors['machine']) : '' ?></p>
        </div>
        <div>
            <label for="weight">重量</label>
            <input type="number" name="weight" id="weight" step="0.1" min="0" max="999.9" value="<?= $result['weight'] ?>"> kg
            <p style="color: red;"><?= isset($errors['weight']) ? escape($errors['weight']) : '' ?></p>
        </div>
        <div>
            <label for="time">回数</label>
            <input type="number" name="time" id="time" min="0" max="99" value="<?= $result['time'] ?>"> 回
            <p style="color: red;"><?= isset($errors['time']) ? escape($errors['time']) : '' ?></p>

        </div>
        <div>
            <label for="set_count">セット</label>
            <input type="number" name="set_count" id="set_count" min="0" max="99" value="<?= $result['set_count'] ?>"> set
            <p style="color: red;"><?= isset($errors['set_count']) ? escape($errors['set_count']) : '' ?></p>
        </div>
        <div>
            <label for="work_load">負荷</label>
            <select name="work_load" id="work_load">
                <option value="">--</option>
                <?php foreach ($form_loads as $form_load) : ?>
                    <!-- 選択されたログの値と合致する場合、selected属性を付加する -->
                    <option value="<?= escape($form_load) ?>" <?= $form_load === $result['work_load'] ? 'selected' : '' ?>><?= escape($form_load) ?></option>
                <?php endforeach ?>
            </select>
            <p style="color: red;"><?= isset($errors['work_load']) ? escape($errors['work_load']) : '' ?></p>
        </div>
        <div>
            <label for="note">メモ</label>
            <input type="text" name="note" id="note" size="50" maxlength="30" value="<?= $result['note'] ?>">
            <p style="color: red;"><?= isset($errors['note']) ? escape($errors['note']) : '' ?></p>

        </div>
        <input type="submit" value="登録">
    </form>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
