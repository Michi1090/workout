<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $message = '';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// セレクトボックスの値
$form_parts = ['肩', '腕', '胸', '腹', '背中', '脚', 'その他'];
$form_loads = ['VERY EASY', 'EASY', 'NORMAL', 'HARD', 'VERY HARD'];

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

    /* バリデーション */
    // 日付
    if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $date)) {
        $errors['date'] = '※日付はカレンダーから選択してください';
    }
    // 部位
    if (!in_array($part, $form_parts, true)) {
        $errors['part'] = '※部位はセレクトボックスから選択してください';
    }
    // マシン
    if (mb_strlen($machine) < 1 || mb_strlen($machine) > 20) {
        $errors['machine'] = '※マシンは20文字以内で入力してください';
    }
    // 重量
    if (!empty($weight) && !preg_match('/^([1-9][0-9]{0,2}|0)(\.[0-9])?$/', $weight)) {
        $errors['weight'] = '※重量は999.9kgまでで登録してください';
    }
    // 回数
    if (!empty($time) && !preg_match('/^[0-9]{1,2}$/', $time)) {
        $errors['time'] = '※回数は99回までで登録してください';
    }
    // セット
    if (!empty($set_count) && !preg_match('/^[0-9]{1,2}$/', $set_count)) {
        $errors['set_count'] = '※SETは99回までで登録してください';
    }
    // 負荷
    if (!empty($work_load) && !in_array($work_load, $form_loads, true)) {
        $errors['work_load'] = '※負荷はセレクトボックスから選択してください';
    }
    // メモ
    if (mb_strlen($note) > 30) {
        $errors['note'] = '※メモは30文字以内で入力してください';
    }

    if (empty($errors)) {
        // 新規レコードを挿入
        $sql = 'INSERT INTO weight_logs (user_id, date, part, machine, weight, time, set_count, work_load, note) VALUES (:user_id, :date, :part, :machine, :weight, :time, :set_count, :work_load, :note)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':part', $part, PDO::PARAM_STR);
        $stmt->bindValue(':machine', $machine, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $weight, PDO::PARAM_INT);
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
            <input type="date" name="date" max="9999-12-31" id="date" required>
            <p style="color: red;"><?= isset($errors['date']) ? escape($errors['date']) : '' ?></p>
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part" required>
                <option value="">--</option>
                <?php foreach ($form_parts as $form_part) : ?>
                    <option value="<?= escape($form_part) ?>"><?= escape($form_part) ?></option>
                <?php endforeach ?>
            </select>
            <p style="color: red;"><?= isset($errors['part']) ? escape($errors['part']) : '' ?></p>
        </div>
        <div>
            <label for="machine">マシン</label>
            <input type="text" name="machine" id="machine" required>
            <p style="color: red;"><?= isset($errors['machine']) ? escape($errors['machine']) : '' ?></p>
        </div>
        <div>
            <label for="weight">重量</label>
            <input type="number" name="weight" id="weight" step="0.1" min="0" max="999.9" value="0"> kg
            <p style="color: red;"><?= isset($errors['weight']) ? escape($errors['weight']) : '' ?></p>
        </div>
        <div>
            <label for="time">回数</label>
            <input type="number" name="time" id="time" min="0" max="99" value="0"> 回
            <p style="color: red;"><?= isset($errors['time']) ? escape($errors['time']) : '' ?></p>

        </div>
        <div>
            <label for="set_count">セット</label>
            <input type="number" name="set_count" id="set_count" min="0" max="99" value="0"> set
            <p style="color: red;"><?= isset($errors['set_count']) ? escape($errors['set_count']) : '' ?></p>
        </div>
        <div>
            <label for="work_load">負荷</label>
            <select name="work_load" id="work_load">
                <option value="">--</option>
                <?php foreach ($form_loads as $form_load) : ?>
                    <option value="<?= escape($form_load) ?>"><?= escape($form_load) ?></option>
                <?php endforeach ?>
            </select>
            <p style="color: red;"><?= isset($errors['work_load']) ? escape($errors['work_load']) : '' ?></p>
        </div>
        <div>
            <label for="note">メモ</label>
            <input type="text" name="note" id="note" size="50" maxlength="30">
            <p style="color: red;"><?= isset($errors['note']) ? escape($errors['note']) : '' ?></p>

        </div>
        <input type="submit" value="登録">
    </form>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/date.js"></script>
</body>

</html>
