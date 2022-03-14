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
} else {
    // ログアウト状態のとき、ログインページへリダイレクトする
    header('Location: users/login.php');
    exit;
}

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
$sql = 'SELECT COUNT(*) as cnt FROM weight_logs  WHERE user_id = :user_id' . $where;
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
$stmt->execute();
$count = $stmt->fetch(PDO::FETCH_ASSOC);
$max_page = (int)ceil($count['cnt'] / 5);

//ページ数の取得。GETでページが渡ってこなかった時(最初のページ)のときは$pageに1を格納する
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
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ヘッダーのパス指定
$path = currentIndex();
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

                <!-- 検索用モーダル -->
                <?php require_once('search_modal.php') ?>

                <!-- 筋トレログ表示 -->
                <?php if (!empty($logs)) : ?>
                    <?php foreach ($logs as $log) : ?>
                        <!-- カード -->
                        <div class="card my-4">
                            <!-- カードヘッダー -->
                            <div class="card-header bg-warning">
                                <div class="row">
                                    <h5 class="col-7 my-2"><?= escape($log['machine']) ?></h5>
                                    <div class="col-5 d-flex justify-content-end align-items-center ps-0 pe-1">
                                        <?php $id = $log['id']; ?>
                                        <a class="btn btn-success rounded-pill me-2" href="edit.php?id=<?= escape($id) ?>">編集</a>
                                        <a class="btn btn-danger rounded-pill" href="delete.php?id=<?= escape($id) ?>">削除</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">日付 : <?= escape($log['date']) ?></p>
                                <div class="row mb-2">
                                    <p class="col mb-0">部位 : <?= escape($log['part']) ?></p>
                                    <p class="col mb-0">重量 : <?= escape($log['weight']) ?> kg</p>
                                </div>
                                <div class="row mb-2">
                                    <p class="col mb-0">回数 : <?= escape($log['time']) ?> 回</p>
                                    <p class="col mb-0">セット : <?= escape($log['set_count']) ?> set</p>
                                </div>
                                <p class="mb-2">負荷 : <?= escape($log['work_load']) ?></p>
                                <p class="mb-0">メモ : <?= escape($log['note']) ?></p>
                            </div>
                        </div>
                    <?php endforeach ?>

                    <!-- ページネーション -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <!-- 戻るボタン -->
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

                    <!-- 該当ログなしの場合  -->
                <?php else : ?>
                    <h6>※該当する筋トレーニングログが存在しません</h6>
                    <a href="create.php">新規トレーニングログ登録</a>
                <?php endif ?>
            </div>
        </div>
    </main>

    <script src="js/bootstrap.bundle.min.js"></script><!-- Bootstrap -->
    <script src="js/clear_conditions.js"></script><!-- 検索条件のクリア -->
</body>

</html>
