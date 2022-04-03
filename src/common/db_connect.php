<?php
// データベース接続

/* 本番環境 */
try {
    // データベースに接続してPDOインスタンスを作成
    $url = parse_url(getenv('CLEARDB_DATABASE_URL'));

    $db_name = substr($url['path'], 1);
    $db_host = $url['host'];
    $charset = 'utf8mb4';
    $dsn = 'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=' . $charset;

    $user = $url['user'];
    $pass = $url['pass'];
    $driver_options = array(PDO::ATTR_PERSISTENT => true);

    $pdo = new PDO($dsn, $user, $pass, $driver_options);
} catch (PDOException $e) {
    // DBアクセスに失敗した場合、エラーメッセージを表示
    echo 'データベース接続エラー : ' . $e->getMessage() . '<br/>時間をおいてから再度お試しください。';
    exit;
}

/* ------------------------------------------------------------------------------------------------------------- */

/* ローカル環境 */
// try {
//     // データベースに接続してPDOインスタンスを作成
//     $dsn = 'mysql:host=localhost:8889;dbname=workout;charset=utf8mb4';
//     $user = 'root';
//     $pass = '';
//     $driver_options = array(PDO::ATTR_PERSISTENT => true);
//     $pdo = new PDO($dsn, $user, $pass, $driver_options);
// } catch (PDOException $e) {
//     // DBアクセスに失敗した場合、エラーメッセージを表示
//     echo 'データベース接続エラー : ' . $e->getMessage() . '<br/>時間をおいてから再度お試しください。';
//     exit;
// }
