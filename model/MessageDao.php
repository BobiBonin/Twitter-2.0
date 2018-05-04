<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 22.4.2018 г.
 * Time: 12:17
 */

namespace model;


class MessageDao extends BaseDao
{

    public function addMessage(Message $message){
        $statement = $this->pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text,message_img) VALUES (?,?,?,?)");
        $statement->execute(array($message->getOwnerId(),
            $message->getReceiverId(),
            $message->getText(),
            $message->getImage()
            ));

        $result = $statement->rowCount();
        return $result;
    } //Добавя съобщение

    public function getMessages($id){
        $statement = $this->pdo->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY message_date DESC");
        $statement->execute(array($id,$id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Взима съобщение

    public function getNewId(){
        $statement = $this->pdo->prepare("SELECT message_id FROM mydb.messages ORDER BY message_id DESC LIMIT 1;");
        $statement->execute(array());
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}

