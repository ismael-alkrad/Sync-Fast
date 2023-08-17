<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', 32767);
include_once '../functions.php';



insertUser($_POST['username'],$_POST['email'], $_POST['password'], 3, 1, $pdo);
