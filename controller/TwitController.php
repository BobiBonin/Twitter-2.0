<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/20/2018
 * Time: 1:33 PM
 */

namespace controller;

use \model\TweetDao;
use \model\UserDao;
use \model\Tweet;
use model\CommentDao;

class TwitController extends BaseController
{
    public function tweets()
    {

        try {
            $dao = new TweetDao();
            $user = $_SESSION['user']->getId();
            $text = htmlentities($_POST['text']);
            $newId = $dao->getNewId();

            $tweet_id = $newId['0']["twat_id"] + 1;
            if ($_FILES['twat_img']['size'] == 0 && $_FILES['twat_img']['error'] == 0) {
                // cover_image is empty (and not an error)
            } else {
                if (isset($_FILES['twat_img']['tmp_name'])) {

                    $url_image = "assets/images/uploads/image_tweet$tweet_id.png";
                    $tmp_image = $_FILES['twat_img']['tmp_name'];
                    $a = getimagesize($tmp_image);
                    $image_type = $a[2];

                    if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
                        if (is_uploaded_file($tmp_image)) {
                            $url_image = "./view/assets/images/uploads/image_tweet$tweet_id.png";
                            if (move_uploaded_file($tmp_image, $url_image)) {
                                $url_image = "assets/images/uploads/image_tweet$tweet_id.png";
                            }
                        } else {
                            $url_image = null;
                        }
                    } else {
                        $url_image = null;
                    }
                } else {
                    $url_image = null;
                }
            }


            $tweet = new Tweet(null, $user, null, $text, $url_image);

            $dao->addTweet($tweet);
            header("location:./view/home.php");
        } catch (\PDOException $e) {
            $this->exception($e);
        }



    }// Добавя нов туит

    public function showOwnTweets()
    {

        try {
            $id = $_SESSION['user']->getId();
            $dao = new TweetDao();
            $dao->showUserTweets($id);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }//Показва туитовете на текущо логнатия потребител




    public function showOtherUsersTweets()
    {

        try {

            $name = htmlentities($_GET['name']);
            $uDao = new UserDao();
            $tDao = new TweetDao();
            $you = $uDao->findId($name);
            $result = $tDao->showMyTweets($you['user_id']);

            foreach ($result as &$tweet) {
                $tweet['likes'] = $tDao->getTweetLikes($tweet['twat_id']);
                $tweet['youLike'] = $tDao->checkIfLiked($_SESSION['user']->getId(), $tweet['twat_id']);
            }
            foreach ($result as &$tweet) {
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $tag = substr($array[$i], 1);
                        $array[$i] = "<a href='home_hashtags.php?$tag' style='font-weight: bold;'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $uDao->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name' onmouseover='info(this)' onmouseout='hide1()' style='font-weight: bold; color: #1da1f2'>$array[$i]</a>";
                        }
                    }
                }
                $tweet['twat_content'] = implode(" ", $array);
            }
            echo json_encode($result);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }// Показва туитовете на посещаваните юзъри.

    public function showMyTweets()
    {

        try {
            $id = $_SESSION['user']->getId();
            $dao = new TweetDao();
            $uDao = new UserDao();
            $result = $dao->showMyTweets($id);

            foreach ($result as &$tweet) {
                $tweet['likes'] = $dao->getTweetLikes($tweet['twat_id']);
                $tweet['youLike'] = $dao->checkIfLiked($id, $tweet['twat_id']);
            }

            foreach ($result as &$tweet) {
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $tag = substr($array[$i], 1);
                        $array[$i] = "<a href='home_hashtags.php?$tag' style='font-weight: bold;'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $uDao->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name' onmouseover='info(this)' onmouseout='hide1()' style='font-weight: bold; color: #1da1f2'>$array[$i]</a>";
                        }
                    }
                }
                $tweet['twat_content'] = implode(" ", $array);
            }
            echo json_encode($result);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }//Показва туитовете на текущо логнатия потребител.

    public function likeTweet()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $tDao = new TweetDao();
                $user_id = $_SESSION['user']->getId();
                $tweet_id = $_GET['twat_id'];
                $username = $_SESSION['user']->getUsername();

                $uDao = new CommentDao();
                $tweetOwner = $uDao->findTweetOwner($tweet_id);
                $message = "$username like your tweet!";
                $status = "unread";

                $tDao->likeATweet($tweet_id, $user_id, $tweetOwner[0]['id'], $message, $status);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }//Харесване на туит.

    public function dislikeTweet()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $dao = new TweetDao();
                $user_id = $_SESSION['user']->getId();
                $twat_id = $_GET['twat_id'];
                $dao->dislikeATweet($twat_id, $user_id);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }// Отхаресване на туит

    public function getTweetLikes()
    {

        try {
            $id = $_GET['id'];
            $dao = new TweetDao();
            $result = $dao->getTweetLikes($id);
            echo json_encode($result);
        } catch (\PDOException $e) {
            $this->exception($e);
        }


    }// Полазва броя на лайковете на даден туит

    public function displayTweets()
    {

        try {
            $uDao = new UserDao();
            $tDao = new TweetDao();

            $user_id = $_SESSION['user']->getId();

            $arr = $uDao->getFollowersId($user_id);

            $string = "twats.user_id = " . $user_id . " OR ";
            for ($i = 0; $i < count($arr); $i++) {
                $string = $string . "twats.user_id = $arr[$i]";
                if ($i < count($arr) - 1) {
                    $string = $string . " OR ";
                }
            }
            $result = $tDao->getMyFollowersTweets($string);
            foreach ($result as &$tweet) {
                $tweet['likes'] = $tDao->getTweetLikes($tweet['twat_id']);
                $tweet['youLike'] = $tDao->checkIfLiked($user_id, $tweet['twat_id']);
            }


            foreach ($result as &$tweet) {
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $tag = substr($array[$i], 1);
                        $array[$i] = "<a href='home_hashtags.php?$tag' style='font-weight: bold;'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $uDao->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name' onmouseover='info(this)' onmouseout='hide1()' style='font-weight: bold; color: #1da1f2'>$array[$i]</a>";
                        }
                    }
                }
                $tweet['twat_content'] = implode(" ", $array);
            }

            echo json_encode($result);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва туитовете.

    public function displayTags()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $user_id = $_SESSION['user']->getId();
                $tag = htmlentities($_GET['tag']);
                $dao = new TweetDao();
                $uDao = new UserDao();
                if(is_numeric($tag)){
                    $result = $dao->getTweet($tag);
                }else{
                    $result = $dao->getHashtags($tag);
                }


                foreach ($result as &$tweet) {
                    $tweet['likes'] = $dao->getTweetLikes($tweet['twat_id']);
                    $tweet['youLike'] = $dao->checkIfLiked($user_id, $tweet['twat_id']);
                }


                foreach ($result as &$tweet) {
                    $array = explode(" ", $tweet['twat_content']);
                    for ($i = 0; $i < count($array); $i++) {
                        if (substr($array[$i], 0, 1) == "#") {
                            $tag = substr($array[$i], 1);
                            $array[$i] = "<a href='home_hashtags.php?$tag' style='font-weight: bold;'>$array[$i]</a>";
                        }

                        if (substr($array[$i], 0, 1) == "@") {
                            $name = substr($array[$i], 1);
                            $exist = $uDao->findId($name);
                            if ($exist !== false) {
                                $array[$i] = "<a href='profile.php?$name' onmouseover='info(this)' onmouseout='hide1()' style='font-weight: bold; color: #1da1f2'>$array[$i]</a>";
                            }
                        }
                    }
                    $tweet['twat_content'] = implode(" ", $array);
                }
                echo json_encode($result);
            }


        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }// Показва туитовете които съдържат определен хаштаг

    public function checkIfLiked()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $userId = $_SESSION['user']->getId();
                $tweet_id = htmlentities($_GET['tweet_id']);
                $tDao = new TweetDao();
                $result = $tDao->checkIfLiked($userId, $tweet_id);
                echo json_encode($result);
            }


        } catch (\PDOException $e) {
            $this->exception($e);
        }

    }// Проверява дали даден туит е харесан
}