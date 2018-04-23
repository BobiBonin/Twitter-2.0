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

class TwitController
{
    public function tweets()
    {

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $user = $_SESSION['user']['id'];
            $text = htmlentities($_POST['text']);

            $tweet = new Tweet(null, $user, null, $text);
            $dao = new TweetDao();
            $dao->addTweet($tweet);
        } catch (Exception $exception) {

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
        } catch (Exception $exception) {

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
            echo json_encode($result);
        } catch (Exception $exception) {

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
                        if($exist !== false){
                            $array[$i] = "<a href='profile.php?$name'>$array[$i]</a>";
                        }
                    }
                }
                $tweet['twat_content'] = implode(" ", $array);
            }
            echo json_encode($result);

        } catch (PDOException $e) {

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
        } catch (PDOException $e) {

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
            $tDao->getMyFollowersTweets($string);
        } catch (Exception $exception) {

        }
    } //Показва туитовете.
}