<?php

session_start();
unset($_SESSION['login_name']);
header('Location: login.php');
exit;
