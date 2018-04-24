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

class MessageController extends Exception
{
    public function addMessage()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $dao = new MessageDao();
            $newId = $dao->getNewId();
            $msgId = $newId['0']["message_id"]+1;
            $ownerId = $_SESSION['user']['id'];
            $text = htmlentities($_POST['text']);
            $receiverId = $_POST['receiverId'];

            if (isset($_FILES['message_img']['tmp_name'])) {

                $url_image = "assets/images/uploads/image_message$msgId.png";
                $tmp_image = $_FILES['message_img']['tmp_name'];


                if (is_uploaded_file($tmp_image)) {
                    $url_image = "./view/assets/images/uploads/image_message$msgId.png";
                    if (move_uploaded_file($tmp_image, $url_image)) {
                        $url_image = "assets/images/uploads/image_message$msgId.png";
                    }
                }
                else{
                    $url_image = null;
                }
            } else {
                $url_image = null;
            }


            $message = new Message($ownerId, $receiverId, $text, $url_image);

            $dao->addMessage($message);
            header("location:./view/home.php");

        } catch (\PDOException $e) {
            $this->exception($e);
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
            $result[] = $temp;

            echo json_encode($result);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
        return 0;
    }

}