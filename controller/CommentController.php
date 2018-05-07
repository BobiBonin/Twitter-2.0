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
use model\UserDao;

class CommentController extends BaseController
{
    public function postComment(){

        try{
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_SESSION['user']->getId();
                $username = $_SESSION['user']->getUsername();
                $content = htmlentities($_POST['content']);
                $tweet_id = htmlentities($_POST['tweetId']);
                if(strlen($content) > 0){
                    $comment = new Comment($tweet_id,$content,$user_id);
                    $dao = new CommentDao();
                    $uDao = new UserDao();
                    $tweetOwner = $dao->findTweetOwner($tweet_id);
                    $message = "$username comment on your tweet!";
                    $status = "unread";
                    $comments = $dao->addComment($comment);

                    if($tweetOwner[0]['id'] !== $user_id){
                        $uDao->sendNotification($user_id,$tweetOwner[0]['id'],$tweet_id,$message,$status);
                    }

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

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $dao = new CommentDao();
                $id = htmlentities($_GET['tweet_id']);
                $comments = $dao->showMyComments($id);
                echo json_encode($comments);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва коментарите на туитовете.
}