<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/20/2018
 * Time: 2:51 PM
 */

namespace controller;
use \model\CommentDao;
use \model\Comment;

class CommentController extends BaseController
{
    public function postComment(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_SESSION['user']['id'];
                $content = $_POST['content'];
                $tweet_id = $_POST['tweetId'];
                if(strlen($content) > 0){
                    $comment = new Comment($tweet_id,$content,$user_id);
                    $dao = new CommentDao();
                    $comments = $dao->addComment($comment);
                    echo json_encode($comments);
                }else{
                    echo json_encode(0);
                }

            }
        } catch (\PDOException $e){
            $this->exception($e);
        }
    } //Публикуване на коментар.

    public function showMyTweetComment(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $dao = new CommentDao();
                $id = $_GET['tweet_id'];
                $comments = $dao->showMyComments($id);
                echo json_encode($comments);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва коментарите на туитовете.
}