<?php
/**
 * Created by PhpStorm.
 * User: user-15
 * Date: 26.04.18
 * Time: 14:59
 */

namespace model;


abstract class BaseDao
{
    protected $pdo;
    const DB_NAME = "mydb";
    const DB_IP = "94.26.37.108";
    const DB_PORT = "3306";
    const DB_USER = "gamigata";
    const DB_PASS = "kaish";


    public function __construct(){
        try {
            $this->pdo = new \PDO("mysql:host=" . self::DB_IP . ":" . self::DB_PORT . ";dbname=" . self::DB_NAME, self::DB_USER, self::DB_PASS);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Problem with db query  - " . $e->getMessage();
        }
    }
}