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

class MessageController extends BaseController
{
    public function addMessage()
    {
        try {
            $dao = new MessageDao();
            $userDao = new UserDao();
            $newId = $dao->getNewId();

            $msgId = $newId['0']["message_id"] + 1;
            $ownerId = $_SESSION['user']['id'];
            $text = htmlentities($_POST['text']);
            $receiverName = htmlentities($_POST['receiverName']);
            $receiverId = $userDao->getUserIdFromName($receiverName);
            if (count($receiverId) > 0) {
                $receiverId = $receiverId['0']['user_id'];
            } else {
                echo "That user does not exist, sorry :)";
                die();
            }
            if (isset($_FILES['message_img']['tmp_name'])) {

                $url_image = "assets/images/uploads/image_message$msgId.png";
                $tmp_image = $_FILES['message_img']['tmp_name'];
                $a = getimagesize($tmp_image);
                $image_type = $a[2];

                if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
                    if (is_uploaded_file($tmp_image)) {
                        $url_image = "./view/assets/images/uploads/image_message$msgId.png";
                        if (move_uploaded_file($tmp_image, $url_image)) {
                            $url_image = "assets/images/uploads/image_message$msgId.png";
                        }
                    } else {
                        $url_image = null;
                    }
                }else{
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
    }// Добавя ново съобщение

    public function getMessages()
    {

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
    }// Извлича всички съобщения на логнатия потребител

}