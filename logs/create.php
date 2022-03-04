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
$form_parts = ['肩', '腕', '胸', '腹', '背中', '脚', '有酸素運動', 'その他'];
$form_loads = ['VERY EASY', 'EASY', 'NORMAL', 'HARD', 'VERY HARD'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') { // GETでアクセスした場合
    // トークンの生成（CSRF対策）
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;

} elseif (!empty($_SESSION['token']) && $_POST['token'] === $_SESSION['token']) { // フォームから値が入力された場合
    // フォームの入力値を代入
    $token = $_POST['token'];
    $date = $_POST['date'];
    $part = $_POST['part'];
    $machine = $_POST['machine'];
    $weight = $_POST['weight'];
    $time = $_POST['time'];
    $set_count = $_POST['set_count'];
    $work_load = $_POST['work_load'];
    $note = $_POST['note'];

    // 新規レコードを挿入
    $sql = 'INSERT INTO weight_logs (user_id, date, part, machine, weight, time, set_count, work_load, note) VALUES(:user_id, :date, :part, :machine, :weight, :time, :set_count, :work_load, :note)';
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
} else { // 正規のアクセスでない場合
    @die('不正なアクセスが実行されました');
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
    <p><?= $message ?></p>

    <!-- セレクトボックス -->
    <form method="post">
        <input type="hidden" name="token" value="<?= escape($token) ?>">
        <div>
            <label for="date">日付</label>
            <input type="date" name="date" max="9999-12-31" id="date" required>
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part" required>
                <option value="">--</option>
                <?php foreach ($form_parts as $form_part) : ?>
                    <option value="<?= escape($form_part) ?>"><?= escape($form_part) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="machine">マシン</label>
            <input type="text" name="machine" id="machine" required>
        </div>
        <div>
            <label for="weight">重量</label>
            <input type="number" name="weight" id="weight" step="0.1">
        </div>
        <div>
            <label for="time">回数</label>
            <input type="number" name="time" id="time">
        </div>
        <div>
            <label for="set_count">セット</label>
            <input type="number" name="set_count" id="set_count">
        </div>
        <div>
            <label for="work_load">負荷</label>
            <select name="work_load" id="work_load">
                <option value="">--</option>
                <?php foreach ($form_loads as $form_load) : ?>
                    <option value="<?= escape($form_load) ?>"><?= escape($form_load) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="note">メモ</label>
            <textarea name="note" id="note"></textarea>
        </div>
        <input type="submit" value="登録">
    </form>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
