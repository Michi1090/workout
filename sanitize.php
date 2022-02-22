<?php

// ユーザー名のバリデーション
function validateName($name)
{
    $message = '';
    if (!preg_match('/[a-zA-Z0-9]{1,30}/', $name)) {
        $message = '※ユーザー名は半角英数字30文字以内で入力してください';
    }
    return $message;
}

// パスワードのバリデーション
function validatePass($pass)
{
    $message = '';
    if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}/', $pass)) {
        $message = '※パスワードは半角英数字8文字以上で、英大文字、英子文字、数字を最低1個以上含む必要があります';
    }
    return $message;
}

// パスワード（確認用）のバリデーション
function validatePassCheck($pass, $pass_check)
{
    $message = '';
    if ($pass !== $pass_check) {
        $message = '※確認用パスワードが一致しません';
    }
    return $message;
}

// HTMLのエスケープ処理
function escapeHtml($before)
{
    foreach ($before as $key => $value) {
        $after[$key] = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
    }
    return $after;
}
