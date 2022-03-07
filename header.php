<!--  ロジック
================================================================================================  -->
<?php require_once('sanitize.php'); ?>


<!--  ビュー
================================================================================================  -->

<header>
    <nav class="navbar navbar-expand navbar-light bg-warning mb-5">
        <div class=container-fluid>
            <a class="navbar-brand site-logo" href="<?= escape($path_logs) ?>index.php">My Workout</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <!-- ここからドロップダウン -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">Menu</a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['id'])) : ?>
                                <a class="dropdown-item" href="<?= escape($path_logs) ?>create.php">トレーニングログ作成</a>
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
