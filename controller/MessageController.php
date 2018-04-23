<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 22.4.2018 г.
 * Time: 12:30
 */

namespace controller;

use \model\MessageDao;
use model\User;
use \model\UserDao;
use \model\Message;

class MessageController
{
    public function addMessage()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $ownerId = $_SESSION['user']['id'];
            $text = htmlentities($_POST['text']);
            $receiverId = $_POST('receiverId');


            $message = new Message($ownerId, $receiverId, $text);
            $dao = new MessageDao();
            $dao->addMessage($message);
        } catch (Exception $exception) {

        }


    }

    public function getMessages()
    {
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $ownerId = $_SESSION['user']['id'];

            $dao = new MessageDao();
            $uDao = new UserDao();
            $dao->getMessages($ownerId);
            $result = $dao->getMessages($ownerId);
            $temp = [];
            foreach ($result as $message) {
                $u1 = new User(null, null, null, null, null, null, null, $message["sender_id"]);
                $u2 = new User(null, null, null, null, null, null, null, $message["receiver_id"]);
                $temp[$message['message_id']]['sender'] = $uDao->getUserInfoById($u1);
                $temp[$message['message_id']]['receiver'] = $uDao->getUserInfoById($u2);
            }
            $result[]=$temp;

            echo json_encode($result);
        } catch (Exception $exception) {
            return 'error';
        }
        return 0;
    }
}