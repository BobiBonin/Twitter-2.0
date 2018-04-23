<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 22.4.2018 г.
 * Time: 12:17
 */

namespace model;


class MessageDao
{
    const DB_NAME = "mydb";
    const DB_IP = "94.26.37.108";
    const DB_PORT = "3306";
    const DB_USER = "gamigata";
    const DB_PASS = "kaish";

    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new \PDO("mysql:host=" . self::DB_IP . ":" . self::DB_PORT . ";dbname=" . self::DB_NAME, self::DB_USER, self::DB_PASS);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Problem with db query  - " . $e->getMessage();
        }
    }

    public function addMessage(Message $message){
        $statement = $this->pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?,?,?)");
        $statement->execute(array($message->getOwnerId(),
            $message->getReceiverId(),
            $message->getText()));

        $result = $statement->rowCount();
        return $result;
    }

    public function getMessages($id){
        $statement = $this->pdo->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ?");
        $statement->execute(array($id,$id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
