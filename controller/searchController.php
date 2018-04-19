<?php
/*Georgi -- 23.03.2018 -- Търси юзър по име LIMIT 5*/
session_start();

use \model\UserDao;
use \model\User;

function __autoload($class)
{
    $class = "..\\" . $class;
    require_once str_replace("\\", "/", $class) . ".php";
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $users = "";
        $name = htmlentities($_GET['name']);
        if(strlen($name) == 0){
            echo json_encode($users);
        }else{
            $dao = new UserDao();
            $email = $_SESSION['user']['email'];
            $users = $dao->getFirstFiveUsersByName($name,$email);
            echo json_encode($users);
        }
    } catch (Exception $exception) {

    }

}
