<?php
session_start();

use model\User;

function __autoload($class)
{
    $class = "..\\" . $class;
    require_once str_replace("\\", "/", $class) . ".php";
}


if (!isset($_SESSION['user'])) {
    header("location:../index.php");
}