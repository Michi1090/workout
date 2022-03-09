<!--  ロジック
================================================================================================  -->
<?php

require_once('../db_connect.php');
require_once('../sanitize.php');

// セッションの開始
session_start();

if (isset($_SESSION['id'])) {
    // ログイン状態のとき
    $user_id = $_SESSION['id'];
    $message = $_SESSION['name'] . ' の筋トレログ一覧';
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: ../users/login.php');
    exit;
}

// セレクトボックスの値
require_once('select_box.php');

// 検索条件の値を代入
$date = filter_input(INPUT_GET, 'date');
$part = filter_input(INPUT_GET, 'part');
$machine = filter_input(INPUT_GET, 'machine');

// 検索条件をクエリーパラメータに格納
$search_conditions = '&date=' . $date . '&part=' . $part . '&machine=' . $machine;

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

//最大ページ数の取得
$count_sql = 'SELECT COUNT(*) as cnt FROM weight_logs  WHERE user_id = :user_id' . $where;
$counts = $pdo->prepare($count_sql);
$counts->bindValue(':user_id', $user_id, PDO::PARAM_INT);
if (!empty($date)) {
    $counts->bindValue(':date', $date, PDO::PARAM_STR);
}

if (!empty($part)) {
    $counts->bindValue(':part', $part, PDO::PARAM_STR);
}

if (!empty($machine)) {
    $counts->bindValue(':machine', '%' . $machine . '%', PDO::PARAM_STR);
}
$counts->execute();
$count = $counts->fetch(PDO::FETCH_ASSOC);
$max_page = (int)ceil($count['cnt'] / 5);

//ページ数を取得する。GETでページが渡ってこなかった時(最初のページ)のときは$pageに１を格納する
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}

// ページ番号
if ($page === 1 || $page === $max_page) {
    $range = 4;
} elseif ($page === 2 || $page === $max_page - 1) {
    $range = 3;
} else {
    $range = 2;
}

// ?件目の表示
$from_record = ($page - 1) * 5 + 1;
if ($page === $max_page && $count['cnt'] % 5 !== 0) {
    $to_record = ($page - 1) * 5 + $count['cnt'] % 5;
} else {
    $to_record = $page * 5;
}

// ページと検索条件に合致するトレーニングログを5件取得
$sql = 'SELECT weight_logs.id, date, part, machine, weight, time, set_count, work_load, note FROM weight_logs JOIN users ON user_id = users.id WHERE user_id = :user_id' . $where . ' ORDER BY date DESC LIMIT :from, 5';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
if (!empty($date)) {
    $stmt->bindValue(':date', $date, PDO::PARAM_STR);
}
if (!empty($part)) {
    $stmt->bindValue(':part', $part, PDO::PARAM_STR);
}
if (!empty($machine)) {
    $stmt->bindValue(':machine', '%' . $machine . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':from', $from_record - 1, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <h2><?= escape($message) ?></h2>

    <!-- 検索フォーム -->
    <form method="get">
        <div>
            <label for="date">日付</label>
            <input type="date" name="date" max="9999-12-31" id="date" value="<?= escape($date) ?>">
        </div>
        <div>
            <label for="part">部位</label>
            <select name="part" id="part">
                <option value="">--</option>
                <?php foreach ($form_parts as $form_part) : ?>
                    <!-- 検索で入力された値とする場合、selected属性を付加する -->
                    <option <?= $form_part === $part ? 'selected' : '' ?>><?= escape($form_part) ?></option>
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
                        <?php $id = $log['id']; ?>
                        <td><?= escape($log['date']) ?></td>
                        <td><?= escape($log['part']) ?></td>
                        <td><?= escape($log['machine']) ?></td>
                        <td><?= escape($log['weight']) . ' kg' ?></td>
                        <td><?= escape($log['time']) . ' 回' ?></td>
                        <td><?= escape($log['set_count']) . ' set' ?></td>
                        <td><?= escape($log['work_load']) ?></td>
                        <td> <?= escape($log['note']) ?></td>
                        <td><a href="edit.php?id=<?= $id ?>">編集</a></td>
                        <td><a href="delete.php?id=<?= $id ?>">削除</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else : ?>
            <p>該当する筋トレログはありません</p>
        <?php endif ?>
    </div>

    <!-- ページネーション -->
    <!-- 戻るボタン -->
    <div class="pagination">
        <?php if ($page >= 2) : ?>
            <a href="index.php?page=<?= escape(($page - 1) . $search_conditions) ?>" class="page_feed">&laquo;</a>
        <?php else : ?>
            <span class="first_last_page">&laquo;</span>
        <?php endif ?>
        <!-- ページ番号 -->
        <?php for ($i = 1; $i <= $max_page; $i++) : ?>
            <?php if ($i >= $page - $range && $i <= $page + $range) : ?>
                <?php if ($i === $page) : ?>
                    <span class="now_page_number"><?= escape($i) ?></span>
                <?php else : ?>
                    <a href="?page=<?= escape($i . $search_conditions) ?>" class="page_number"><?= escape($i) ?></a>
                <?php endif ?>
            <?php endif ?>
        <?php endfor ?>
        <!-- 進む -->
        <?php if ($page < $max_page) : ?>
            <a href="index.php?page=<?= escape(($page + 1) . $search_conditions) ?>" class="page_feed">&raquo;</a>
        <?php else : ?>
            <span class="first_last_page">&raquo;</span>
        <?php endif; ?>
    </div>
    <!-- ?件目の表示 -->
    <p class="from_to"><?= escape($count['cnt'] . '件中' . $from_record . '-' . $to_record . '件目を表示') ?></p>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
