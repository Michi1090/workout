<?php

// ユーザー名のバリデーション
function nameCheck($pass)
{
    $message = '';
    if (!preg_match('/[a-zA-Z0-9]{1,30}/', $pass)) {
        $message = '入力内容が適切ではありません。';
    }
    return $message;
}

// パスワードのバリデーション
function passCheck($pass)
{
    $message = '';
    if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,20}/', $pass)) {
        $message = '入力内容が適切ではありません';
    }
    return $message;
}

// HTMLのエスケープ処理
function htmlEscape($before)
{
    foreach ($before as $key => $value) {
        $after[$key] = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }
    return $after;
}
