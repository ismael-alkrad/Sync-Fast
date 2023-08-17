<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', 32767);
include_once '../functions.php';

checkVerificationCode($_POST['v_code'],$_POST['email'], $pdo);