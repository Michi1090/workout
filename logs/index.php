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
$current_index = 'set';
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

                <!-- 検索用モーダル -->
                <?php require_once('search_modal.php') ?>

                <!-- 筋トレログ表示 -->
                <?php if (!empty($result)) : ?>
                    <?php foreach ($result as $log) : ?>
                        <!-- カード -->
                        <div class="card my-4">
                            <!-- カードヘッダー -->
                            <div class="card-header bg-warning">
                                <div class="row">
                                    <h5 class="col my-2"><?= escape($log['machine']) ?></h5>
                                    <div class="col text-end">
                                        <?php $id = $log['id']; ?>
                                        <a class="btn btn-success" href="edit.php?id=<?= escape($id) ?>">編集</a>
                                        <a class="btn btn-danger" href="delete.php?id=<?= escape($id) ?>">削除</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p class="col">日付 : <?= escape($log['date']) ?></p>
                                    <p class="col">部位 : <?= escape($log['part']) ?></p>
                                </div>
                                <div class="row">
                                    <p class="col-5"><?= escape($log['weight']) . ' kg' ?></p>
                                    <p class="col-3"><?= escape($log['time']) . ' 回' ?></p>
                                    <p class="col-1">×</p>
                                    <p class="col-3"><?= escape($log['set_count']) . ' set' ?></p>
                                </div>
                                <p>負荷 : <?= escape($log['work_load']) ?></p>
                                <p>メモ : <?= escape($log['note']) ?></p>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else : ?>
                    <p>該当する筋トレログはありません</p>
                <?php endif ?>
            </div>
        </div>

        <!-- ページネーション -->
        <nav>
            <!-- 戻るボタン -->
            <ul class="pagination justify-content-center">
                <?php if ($page >= 2) : ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=1<?= escape($search_conditions) ?>">&laquo;</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?= escape(($page - 1) . $search_conditions) ?>">&lt; 前ページ</a>
                    </li>
                <?php else : ?>
                    <li class="page-item">
                        <span class="page-link">&laquo;</span>
                    </li>
                    <li class="page-item">
                        <span class="page-link">&lt; 前ページ</span>
                    </li>
                <?php endif ?>
                <!-- 進むボタン -->
                <?php if ($page < $max_page) : ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?= escape(($page + 1) . $search_conditions) ?>">次ページ &gt;</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?= escape($max_page . $search_conditions) ?>">&raquo;</a>
                    </li>
                <?php else : ?>
                    <li class="page-item">
                        <span class="page-link">次ページ &gt;</span>
                    </li>
                    <li class="page-item">
                        <span class="page-link">&raquo;</span>
                    </li>
                <?php endif ?>
            </ul>
            <!-- ?件目の表示 -->
            <p class="text-center"><?= escape($count['cnt'] . '件中' . $from_record . '-' . $to_record . '件目を表示') ?></p>
        </nav>

    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>
