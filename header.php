<!--  ロジック
================================================================================================  -->
<?php require_once('sanitize.php'); ?>


<!--  ビュー
================================================================================================  -->

<header>
    <nav class="navbar navbar-expand navbar-light bg-warning mb-4">
        <div class=container-fluid>
            <a class="navbar-brand site-logo" href="<?= escape($path_logs) ?>index.php">
                <i class="fa-solid fa-dumbbell me-1"></i>My Workout
            </a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <!-- インデックスページの時のみ検索アイコンを表示 -->
                    <?php if (isset($current_index)) : ?>
                        <li class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#search-modal"><i class="fa-solid fa-magnifying-glass"></i></li>
                    <?php endif ?>
                    <!-- ここからドロップダウン -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">Menu</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- ログイン状態によってメニュー内容を切り替える -->
                            <?php if (isset($_SESSION['id'])) : ?>
                                <a class="dropdown-item" href="<?= escape($path_logs) ?>create.php">トレーニングログ登録</a>
                                <a class="dropdown-item" href="<?= escape($path_users) ?>my_page.php">マイページ</a>
                                <a class="dropdown-item" href="<?= escape($path_users) ?>logout.php">ログアウト</a>
                            <?php else : ?>
                                <a class="dropdown-item" href="<?= escape($path_users) ?>login.php">ログイン</a>
                                <a class="dropdown-item" href="<?= escape($path_users) ?>sign_up.php">ユーザー登録</a>
                            <?php endif ?>
                        </div>
                    </li><!-- /ここまでドロップダウン -->
                </ul>
            </div>
        </div>
    </nav>
</header>
