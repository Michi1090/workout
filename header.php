<!--  ロジック
================================================================================================  -->
<?php require_once('sanitize.php'); ?>


<!--  ビュー
================================================================================================  -->
<header>
    <nav>
        <h1><a href="<?= escape($path_logs) ?>index.php">My Workout</a></h1>
        <ul>
            <?php if (isset($_SESSION['id'])) : ?>
                <li><a href="<?= escape($path_logs) ?>create.php">トレーニングログ作成</a></li>
                <li><a href="<?= escape($path_users) ?>my_page.php">ログインユーザー : <?= escape($_SESSION['name']) ?></a></li>
                <li><a href="<?= escape($path_users) ?>logout.php">ログアウト</a></li>
            <?php else : ?>
                <li><a href="<?= escape($path_users) ?>login.php">ログイン</a></li>
                <li><a href="<?= escape($path_users) ?>sign_up.php">ユーザー登録</a></li>
            <?php endif ?>
        </ul>
    </nav>
</header>
