<?php

require_once('sanitize.php');

if (isset($_SESSION['id'])) {
    $name = $_SESSION['name'];
} else {
    $name = '';
}
?>

<header>
    <h1><a href="<?= escape($path_logs) ?>index.php">My Workout</a></h1>
    <nav>
        <ul>
            <li><a href="<?= escape($path_users) ?>my_page.php">ログインユーザー : <?= escape($name) ?></a></li>
            <li><a href="<?= escape($path_users) ?>login.php">ログイン</a></li>
            <li><a href="<?= escape($path_users) ?>logout.php">ログアウト</a></li>
            <li><a href="<?= escape($path_users) ?>sign_up.php">新規登録</a></li>
            <li><a href="<?= escape($path_logs) ?>create.php">トレーニングログ作成</a></li>
        </ul>
    </nav>
</header>
