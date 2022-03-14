<?php

// データベース接続
try {
    // データベースに接続してPDOインスタンスを作成
    $dsn = 'mysql:host=localhost;dbname=workout;charset=utf8mb4';
    $user = 'root';
    $pass = '';
    $driver_options = array(PDO::ATTR_PERSISTENT => true);
    $pdo = new PDO($dsn, $user, $pass, $driver_options);
} catch (PDOException $e) {
    // DBアクセスに失敗した場合、エラーメッセージを表示
    echo 'データベース接続エラー : ' . $e->getMessage() . '<br/>時間をおいてから再度お試しください。';
    exit;
}

?>
