<?php

// データベース接続
try {
    // データベースに接続してPDOインスタンスを作成
    $dsn = 'mysql:host=localhost;dbname=workout;charset=utf8';
    $pdo = new PDO($dsn, 'root', '');
} catch (PDOException $e) {
    // DBアクセスに失敗した場合、エラーメッセージを表示
    echo 'データベース接続エラー : ' . $e->getMessage() . '<br/>時間をおいてから再度お試しください。';
    exit;
}

?>
