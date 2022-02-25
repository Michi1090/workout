<?php

require_once('sanitize.php');

if (isset($_SESSION['id'])) {
    $name = $_SESSION['name'];
} else {
    $name = '';
}
?>

<header>
    <h1><a href="index.php">My Workout</a></h1>
    <nav>
        <ul>
            <li><a href="my_page.php">ログインユーザー : <?= escape($name) ?></a></li>
            <li><a href="login.php">ログイン</a></li>
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="sign_up.php">新規登録</a></li>
        </ul>
    </nav>
</header>
