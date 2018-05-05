<?php
session_start();

use model\User;

function __autoload($class)
{
    $class = "..\\" . $class;
    require_once str_replace("\\", "/", $class) . ".php";
}

$email = $_SESSION['user']->getEmail();
if (!isset($email)) {
    header("location:../index.php");
}