<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/20/2018
 * Time: 1:18 PM
 */

namespace controller;

use Couchbase\Exception;
use \model\UserDao;
use \model\User;

class UserController extends BaseController
{
    public function login()
    {

        if (isset($_POST['login_btn'])) {
            $email = htmlentities($_POST['email']);
            $password = htmlentities($_POST['password']);

            try {
                $user = new User($email, sha1($password));
                $pdo = new UserDao();
                $result = $pdo->checkUserExist($user);

                if ($result) {
                    $info = $pdo->getUserInfoByEmail($user);

                    $_SESSION['user'] = $info;
                    header("Location: ./view/home.php");
                } else {
                    header("Location: ./view/error_login.html");
                }
            } catch (\PDOException $e) {
                $this->exception($e);
            }
        }

    } //Вход в системата

    public function registration()
    {
        if (isset($_POST['reg_btn'])) {
            $email = htmlentities($_POST['email']);
            $password = htmlentities($_POST['password']);
            $rpassword = htmlentities($_POST['rpassword']);
            $username = htmlentities($_POST['username']);
            $error = false;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = true;
            }
            if ($email == "" || $email == null) {
                $error = true;
            }
            if ($password == "" || $password == null) {
                $error = true;
            }
            if ($rpassword == "" || $rpassword == null) {
                $error = true;
            }
            if ($password !== $rpassword) {
                $error == true;
            }
            if ($username == "" || $username == null) {
                $error = true;
            }
            if (strlen($username) < 3 && strlen($username) > 20) {
                $error == true;
            }
            try {
                if (!$error) {
                    $img_ulr = "assets/images/uploads/default_icon.jpg";
                    $cover_ulr = 'assets/images/default_cover.jpg';
                    $date = date("Y/m/d H:i:s");
                    $user = new User($email, sha1($password), $username, $img_ulr, $cover_ulr, $date);
                    $dao = new UserDao();
                    $result = $dao->userExistForReg($user);
                    if (!$result) {
                        $result = $dao->registerUser($user);
                        $_SESSION['user'] = $result;
                        header("location: ./view/home.php");
                    } else {
                        header("location: ./index.php?exist"); //User all ready EXIST!!
                    }
                } else {
                    header("location: ./index.php?error"); // ERRORS!
                }
            } catch (\PDOException $e) {
                $this->exception($e);
                header("location: ../view/exception_page.php");
            }
        }
    } //Регистрация

    public function userById()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $id = htmlentities($_GET['id']);
                $user = new User(null, null, null, null, null, null, null, null, $id);
                $dao = new UserDao();
                $json = $dao->getUserInfoById($user);
                echo json_encode($json);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }

    } //Взима информация за юзър по айди

    public function showSmallDiv()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $name = $_GET['name'];

                $user = new User(null, null, $name);
                $pdo = new UserDao();

                $result = $pdo->getUserFollowings($user);
                $digits[] = $result;
                $result = $pdo->getUserFollowers($user);
                $digits[] = $result;
                $result = $pdo->getUserTwits($user);
                $digits[] = $result;
                echo json_encode($digits);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } // Показва допълнителна информация за посочения юзър от долната функция.

    public function showRandomUsers()
    {

        try {
            $dao = new UserDao();
            $session = &$_SESSION['user'];
            $email = $session->getEmail();
            $random_users = $dao->getFourRandomUsers($email);
            echo json_encode($random_users);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва 3 рандом юзъри. !!!!!!!!!!! --> СМЕНИ ЗАЯВКАТА ДА СЕ ПОКАЗВАТ САМО ТАКИВА КОИТО НЕ СИ ПОСЛЕДВАЛ !!!!!!

    public function showProfile()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = htmlentities($_GET['name']);
                $session = &$_SESSION['user'];
                if ($name == $session->getUsername()) {
                    $response = "my";
                    echo json_encode($response);
                } else {
                    $user = new User(null, null, $name);
                    $dao = new UserDao();
                    $info = $dao->getUserInfoByName($user);
                    if ($info == null) {
                        $response = "my";
                        echo json_encode($response);
                    } else {
                        echo json_encode($info);
                    }
                }
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Извлича нужната информация за попълване на профил.

    public function getInfoForTweets()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = htmlentities($_GET['name']);

                $user = new User(null, null, $name);
                $dao = new UserDao();
                $info = $dao->getUserInfoByName($user);

                echo json_encode($info);


            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Взима информация за юзър по име

    public function showOtherUserFollowings()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $dao = new UserDao();
                $name = $_GET['name'];
                $id = $dao->findId($name);
                $result = $dao->findFollowing($id['user_id']);
                echo json_encode($result);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    }//Показва хората които следват другите юзъри.

    public function showMyProfile()
    {

        try {

            $session = &$_SESSION['user'];
            $name = $session->getUsername();

            $user = new User(null, null, $name);
            $pdo = new UserDao();

            $result = $pdo->getUserFollowings($user);
            $digits[] = $result;
            $result = $pdo->getUserFollowers($user);
            $digits[] = $result;
            $result = $pdo->getUserTwits($user);
            $digits[] = $result;
            echo json_encode($digits);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва моя профил.

    public function showMyFollowers()
    {
        try {
            $session = &$_SESSION['user'];
            $id = $session->getId();
            $dao = new UserDao();
            $result = $dao->findFollowers($id);
            echo json_encode($result);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва моите последователи.

    public function showFollowings()
    {

        try {
            $dao = new UserDao();
            $session = &$_SESSION['user'];
            $id = $session->getId();
            $result = $dao->findFollowing($id);
            echo json_encode($result);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Изкарва информация за всички които следва.

    public function showFollowers()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = $_GET['name'];
                $dao = new UserDao();
                $id = $dao->findId($name);
                $result = $dao->findFollowers($id['user_id']);

                echo json_encode($result);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Изкарва информация за всички последователи.

    public function searchUserAndTags()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $users = "";
                $name = htmlentities($_GET['name']);

                if (strlen($name) == 0) {
                    echo json_encode($users);
                } else {
                    $hashtags = [];
                    $dao = new UserDao();
                    $session = &$_SESSION['user'];
                    $email = $session->getEmail();
                    $users = $dao->getFirstFiveUsersByName($name, $email);
                    foreach ($users[0] as $twat) {
                        $words = explode(" ", $twat['twat_content']);
                        foreach ($words as $word) {
                            if (strstr($word, '#') && strstr($word, $name)) {
                                $hashtags[] = $word;
                            }
                        }
                    }
                    unset($users[0]);
                    $users[0] = array_unique($hashtags);
                    echo json_encode($users);
                }
            } catch (\PDOException $e) {
                $this->exception($e);
            }

        }
    } //Търси юзъри и тагове.

    public function profile()
    {
        try {

            $user = new User($_SESSION['user']->getEmail());
            $dao = new UserDao();
            $result = $dao->getUserInfoByEmail($user);
            echo json_encode($result);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Взима информация за юзъра по имейл.

    public function followUser()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $session = &$_SESSION['user'];
                $me = $session->getId();
                $myname = $session->getEmail();
                $name = htmlentities($_GET['name']);
                $dao = new UserDao();
                $you = $dao->findId($name);
                $message = "$myname start following you!";
                $status = "unread";
                $like = $dao->likeIt($me, $you['user_id'], $message, $status);
                echo json_encode($like);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Последвай юзър.

    public function isFollow()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $session = &$_SESSION['user'];
                $my = $session->getId();
                $name = htmlentities($_GET['name']);
                $dao = new UserDao();
                $id = $dao->findId($name);
                $result = $dao->isFollow($my, $id['user_id']);
                echo json_encode($result);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Проверява дали посетения юзър е вече последван или не (за бутона).

    public function getFFT()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = htmlentities($_GET['name']);
                $user = new User(null, null, $name);
                $pdo = new UserDao();

                $digits[] = $pdo->getUserFollowings($user);
                $digits[] = $pdo->getUserFollowers($user);
                $digits[] = $pdo->getUserTwits($user);
                echo json_encode($digits);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } // Взима последователите,следващите и туитовете на юзъра по име.

    public function editProfile()
    {

        if (isset($_POST['btn_edit'])) {
            try {
                var_dump($_SESSION['user']);
                $username = htmlentities($_POST['username']);
                $email = htmlentities($_POST['email']);
                $password = htmlentities($_POST['password']);
                $city = htmlentities($_POST['city']);
                $description = htmlentities($_POST['description']);
                $url_image = "assets/images/default_icon.jpg";
                $tmp_image = $_FILES['user_pic']['tmp_name'];
                $url_cover = "assets/images/default_cover.jpg";
                $tmp_cover = $_FILES["user_cover"]["tmp_name"];


                if ($_SESSION['user']->getImageUrl() !== $url_image) {
                    $url_image = "assets/images/uploads/image_$email.png";
                }
                if ($_SESSION['user']->getCoverUrl() !== $url_cover) {
                    $url_cover = "assets/images/uploads/cover_$email.png";
                }


                $user = new User($_SESSION['user']->getEmail(), sha1($password));
                $pdo = new UserDao();
                $result = $pdo->checkUserExist($user);

                if ($result) {
                    if (is_uploaded_file($tmp_image)) {
                        $url_image = "./view/assets/images/uploads/image_$email.png";
                        if (move_uploaded_file($tmp_image, $url_image)) {
                            $url_image = "assets/images/uploads/image_$email.png";
                        }
                    }
                    if (is_uploaded_file($tmp_cover)) {
                        $url_cover = "./view/assets/images/uploads/cover_$email.png";
                        if (move_uploaded_file($tmp_cover, $url_cover)) {
                            $url_cover = "assets/images/uploads/cover_$email.png";
                        }
                    }
                    $id = $_SESSION['user']->getId();
                    $date = $_SESSION['user']->getDate();
                    $user = new User($email, sha1($password), $username, $url_image, $url_cover, $date, $city, $description, $id);

                    $_SESSION['user'] = $user;
                    var_dump($_SESSION['user']);
                    $pdo = new UserDao();
                    $pdo->updateUser($user);
                    header("location: ./view/profile.php");
                } else {
                    header("location: ./view/profile_error.php");
                }


            } catch (\PDOException $e) {
                $this->exception($e);
            }

        }

    } //Редактиране на профил.

    public function unfollowUser()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $session = &$_SESSION['user'];
                $me = $session->getId();
                $name = $_GET['name'];
                $dao = new UserDao();
                $you = $dao->findId($name);
                $dislike = $dao->dislikeIt($me, $you['user_id']);
                echo json_encode($dislike);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Отхаресване на юзър.

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            session_destroy();
            header('location: index.php');
        }
    } //Прекратяване на сесията и препращане към index.php.

    public function getNotifications()
    {
        try {
            $session = &$_SESSION['user'];
            $myId = $session->getId();
            $uDao = new UserDao();
            $result = $uDao->getNotifications($myId);
            echo json_encode($result);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Взима известията

    public function seeNotification()
    {
        $id = htmlentities($_GET['id']);
        try {
            $uDao = new UserDao();
            $uDao->seeNotification($id);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Променя статуса на известията на прочетени
}