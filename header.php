<?php

require_once('sanitize.php');

if (isset($_SESSION['id'])) {
    $name = $_SESSION['name'];
} else {
    $name = '';
}
?>

<header>
    <h1><a href="<?= escape($path_log) ?>index.php">My Workout</a></h1>
    <nav>
        <ul>
            <li><a href="<?= escape($path_user) ?>my_page.php">ログインユーザー : <?= escape($name) ?></a></li>
            <li><a href="<?= escape($path_user) ?>login.php">ログイン</a></li>
            <li><a href="<?= escape($path_user) ?>logout.php">ログアウト</a></li>
            <li><a href="<?= escape($path_user) ?>sign_up.php">新規登録</a></li>
        </ul>
    </nav>
</header>
