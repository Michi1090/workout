<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $message = $_SESSION['name'] . ' の筋トレログ一覧';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// セレクトボックスの値を取得
$sql = 'SELECT part, name FROM machines';
$stmt = $pdo->query($sql);
$machines = $stmt->fetchAll();

// ユーザーの全筋トレログを取得
$sql = 'SELECT created_at, part, machines.name, weight, time, work_load, note FROM weight_logs JOIN users JOIN machines ON user_id = users.id AND machine_id = machines.id WHERE user_id = :user_id ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();

// フォームから値が入力された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームの入力値を代入
    $created_at = $_POST['created_at'];
    $part = $_POST['part'];
    $machine_name = $_POST['machine_name'];

    // 条件に合致するレコードを取得
    $sql = 'SELECT created_at, part, machines.name, weight, time, work_load, note FROM weight_logs JOIN users JOIN machines ON user_id = users.id AND machine_id = machines.id WHERE user_id = :user_id AND created_at = :created_at AND part = :part AND machines.name = :machine_name';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
    $stmt->bindValue(':part', $part, PDO::PARAM_STR);
    $stmt->bindValue(':machine_name', $machine_name, PDO::PARAM_STR);
    $stmt->execute();
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

    <h2><?= escape($message); ?></h2>

    <!-- セレクトボックス -->
    <form method="post">
        <div>
            <label for="created_at">日付</label>
            <input type="date" name="created_at" max="9999-12-31" id="created_at">
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part">
                <option value="">--</option>
                <?php foreach ($machines as $machine) : ?>
                    <option value="<?= $machine['part'] ?>"><?= $machine['part'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="machine_name">マシン</label>
            <select name="machine_name" id="machine_name">
                <option value="">--</option>
                <?php foreach ($machines as $machine) : ?>
                    <option value="<?= $machine['name'] ?>"><?= $machine['name'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <input type="submit" value="送信">
    </form>


    <div>
        <table>
            <tr>
                <th>日付</th>
                <th>部位</th>
                <th>マシン</th>
                <th>重量</th>
                <th>回数</th>
                <th>負荷</th>
                <th>メモ</th>
            </tr>
            <?php foreach ($stmt as $log) : ?>
                <tr>
                    <td><?= $log['created_at'] ?></td>
                    <td><?= $log['part'] ?></td>
                    <td><?= $log['name'] ?></td>
                    <td><?= $log['weight'] ?></td>
                    <td><?= $log['time'] ?></td>
                    <td><?= $log['work_load'] ?></td>
                    <td> <?= $log['note'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
