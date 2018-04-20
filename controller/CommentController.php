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

class CommentController
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
                $comment = new Comment($tweet_id,$content,$user_id);
                $dao = new CommentDao();
                $comments = $dao->addComment($comment);
                echo json_encode($comments);
            }
        } catch (Exception $exception){

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
        } catch (Exception $exception) {

        }
    } //Показва коментарите на туитовете.
}