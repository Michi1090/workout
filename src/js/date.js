'use strict';

// 日付入力欄にデフォルトで本日の日付が入力される
const today = new Date();
today.setDate(today.getDate());
const yyyy = today.getFullYear();
const mm = ('0' + (today.getMonth() + 1)).slice(-2);
const dd = ('0' + today.getDate()).slice(-2);
document.getElementById('date').value = yyyy + '-' + mm + '-' + dd;
