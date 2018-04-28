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

class TwitController extends BaseController
{
    public function tweets()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $dao = new TweetDao();
            $user = $_SESSION['user']['id'];
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


    }// ???

    public function showOwnTweets()
    {
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $id = $_SESSION['user']['id'];
            $dao = new TweetDao();
            $dao->showUserTweets($id);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }// ????

    public function showOtherUsersTweets()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {

            $name = htmlentities($_GET['name']);
            $uDao = new UserDao();
            $tDao = new TweetDao();
            $you = $uDao->findId($name);
            $result = $tDao->showMyTweets($you['user_id']);
            foreach ($result as &$tweet) {
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $array[$i] = "<a href='#'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $uDao->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name'>$array[$i]</a>";
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
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $id = $_SESSION['user']['id'];
            $dao = new TweetDao();
            $pdo = new UserDao();
            $result = $dao->showMyTweets($id);

            foreach ($result as &$tweet) {
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $array[$i] = "<a href='#'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $pdo->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name'>$array[$i]</a>";
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

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $dao = new TweetDao();
                $user_id = $_SESSION['user']['id'];
                $twat_id = $_GET['twat_id'];
                $dao->likeATweet($twat_id, $user_id);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Харесване не туит.

    public function displayTweets()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $uDao = new UserDao();
            $tDao = new TweetDao();

            $user_id = $_SESSION['user']['id'];

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
                $array = explode(" ", $tweet['twat_content']);
                for ($i = 0; $i < count($array); $i++) {
                    if (substr($array[$i], 0, 1) == "#") {
                        $array[$i] = "<a href='#' style='font-weight: bold;'>$array[$i]</a>";
                    }

                    if (substr($array[$i], 0, 1) == "@") {
                        $name = substr($array[$i], 1);
                        $exist = $uDao->findId($name);
                        if ($exist !== false) {
                            $array[$i] = "<a href='profile.php?$name' onmouseover='info(this)' onmouseout='hide()' style='font-weight: bold; color: #1da1f2'>$array[$i]</a>";
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
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $tag = htmlentities($_GET['tag']);
                $dao = new TweetDao();
                $tweetsWithTag = $dao->getHashtags($tag);
                echo json_encode($tweetsWithTag);
            }


        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }
}