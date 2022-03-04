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

// セレクトボックスの値
$form_parts = ['肩', '腕', '胸', '腹', '背中', '脚', '有酸素運動', 'その他'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') { // GETでアクセスした場合
    // トークンの生成（CSRF対策）
    $token = bin2hex(random_bytes(32));
    $_SESSION['token'] = $token;

    // 入力フォームの初期値
    $date = $part = $machine = '';

    // ユーザーの全筋トレログを取得
    $sql = 'SELECT date, part, machine, weight, time, set_count, work_load, note FROM weight_logs JOIN users ON user_id = users.id WHERE user_id = :user_id ORDER BY date DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
} elseif (!empty($_SESSION['token']) && $_POST['token'] === $_SESSION['token']) { // フォームから値が入力された場合
    // フォームの入力値を代入
    $token = $_POST['token'];
    $date = $_POST['date'];
    $part = $_POST['part'];
    $machine = $_POST['machine'];

    // 条件（where句）の生成
    $where = "";
    if (!empty($date)) {
        $where .= ' AND date = :date';
    }

    if (!empty($part)) {
        $where .= ' AND part = :part';
    }

    if (!empty($machine)) {
        $where .= ' AND machine LIKE :machine';
    }

    // 条件に合致するレコードを取得
    $sql = 'SELECT date, part, machine, weight, time, set_count, work_load, note FROM weight_logs JOIN users ON user_id = users.id WHERE user_id = :user_id' . $where . ' ORDER BY date DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    if (!empty($date)) {
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
    }

    if (!empty($part)) {
        $stmt->bindValue(':part', $part, PDO::PARAM_STR);
    }

    if (!empty($machine)) {
        $stmt->bindValue(':machine', '%' . $machine . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetchAll();
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

    <h2><?= escape($message); ?></h2>

    <!-- セレクトボックス -->
    <form method="post">
        <input type="hidden" name="token" value="<?= escape($token) ?>">
        <div>
            <label for="date">日付</label>
            <input type="date" name="date" max="9999-12-31" id="date" value="<?= escape($date) ?>">
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part">
                <option value="">--</option>
                <?php foreach ($form_parts as $form_part) : ?>
                    <!-- POSTで選択された$partと値が合致する場合、selected属性を付加する -->
                    <option value="<?= escape($form_part) ?>" <?= $form_part === $part ? 'selected' : '' ?>><?= escape($form_part) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="machine">マシン</label>
            <input type="text" name="machine" value="<?= escape($machine) ?>">
        </div>
        <input type="submit" value="検索">
        <input type="button" value="クリア" onclick="location.href='index.php'">
    </form>

    <!-- 筋トレログ表示 -->
    <div>
        <?php if (!empty($result)) : ?>
            <table>
                <tr>
                    <th>日付</th>
                    <th>部位</th>
                    <th>マシン</th>
                    <th>重量</th>
                    <th>回数</th>
                    <th>セット</th>
                    <th>負荷</th>
                    <th>メモ</th>
                </tr>
                <?php foreach ($result as $log) : ?>
                    <tr>
                        <td><?= escape($log['date']) ?></td>
                        <td><?= escape($log['part']) ?></td>
                        <td><?= escape($log['machine']) ?></td>
                        <td><?= escape($log['weight']) . ' kg' ?></td>
                        <td><?= escape($log['time']) . ' 回' ?></td>
                        <td><?= escape($log['set_count']) . ' SET' ?></td>
                        <td><?= escape($log['work_load']) ?></td>
                        <td> <?= escape($log['note']) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else : ?>
            <p>該当する筋トレログはありません</p>
        <?php endif ?>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
