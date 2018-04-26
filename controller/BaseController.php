<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/24/2018
 * Time: 3:28 PM
 */

namespace controller;


abstract class BaseController
{
    protected function exception($e){
        $today = date("F j, Y, g:i a");
        $file = file_get_contents("view/errors.txt");
        $file .= "\n On $today Error Message : ".$e->getMessage();
        file_put_contents("view/errors.txt",$file);
        echo json_encode("exception");
    }
}