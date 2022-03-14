<?php

/* トレーニングログ登録時のバリデーション */
// 日付
if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $date)) {
    $errors['date'] = '※日付はカレンダーから選択してください';
}
// 部位
if (!in_array($part, $form_parts, true)) {
    $errors['part'] = '※部位はセレクトボックスから選択してください';
}
// マシン
if (mb_strlen($machine) < 1 || mb_strlen($machine) > 20) {
    $errors['machine'] = '※マシンは1～20文字で登録してください';
}
// 重量
if (!empty($weight) && !preg_match('/^([0-9]{1,3})(\.[0-9])?$/', $weight)) {
    $errors['weight'] = '※重量は0～999.9kgで登録してください';
}
// 回数
if (!empty($time) && !preg_match('/^[0-9]{1,2}$/', $time)) {
    $errors['time'] = '※回数は0～99回で登録してください';
}
// セット
if (!empty($set_count) && !preg_match('/^[0-9]{1,2}$/', $set_count)) {
    $errors['set_count'] = '※セットは0～99回で登録してください';
}
// 負荷
if (!empty($work_load) && !in_array($work_load, $form_loads, true)) {
    $errors['work_load'] = '※負荷はセレクトボックスから選択してください';
}
// メモ
if (mb_strlen($note) > 30) {
    $errors['note'] = '※メモは30文字以内で入力してください';
}
