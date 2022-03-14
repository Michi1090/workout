<?php

// HTML出力前のエスケープ処理
function escape($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
