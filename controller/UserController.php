<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/20/2018
 * Time: 1:18 PM
 */

namespace controller;
use \model\UserDao;
use \model\User;
class UserController extends Exception
{
    public function login(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        if (isset($_POST['login_btn'])) {
            $email = htmlentities($_POST['email']);
            $password = htmlentities($_POST['password']);

            try {
                $user = new User($email, sha1($password));
                $pdo = new UserDao();
                $result = $pdo->checkUserExist($user);

                if ($result) {
                    $info = $pdo->getUserInfoByEmail($user);
                    $_SESSION['user'] = [];
                    $new = [
                        "id" => $info['user_id'],
                        "name" => $info['user_name'],
                        "reg_date" => $info['user_date'],
                        "image" => $info['user_pic'],
                        "cover" => $info['user_cover'],
                        "city" => $info['user_city'],
                        "description" => $info['user_description'],
                        "email" => $email,
                    ];
                    $_SESSION['user'] = $new;
                    header("Location: ./view/home.php");
                } else {
                    header("Location: ./view/error_login.html");
                }
            } catch (\PDOException $e) {
                $this->exception($e);
            }
        }

    }

    public function registration(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        if (isset($_POST['reg_btn'])) {
            $email = htmlentities($_POST['email']);
            $password = htmlentities($_POST['password']);
            $rpassword = htmlentities($_POST['rpassword']);
            $username = htmlentities($_POST['username']);
            $error = false;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = true;
            }
            if($email == "" || $email == null){
                $error = true;
            }
            if($password == "" || $password == null){
                $error = true;
            }
            if($rpassword == "" || $rpassword == null){
                $error = true;
            }
            if ($password !== $rpassword) {
                $error == true;
            }
            if($username == "" || $username == null){
                $error = true;
            }
            if (strlen($username) < 3 && strlen($username) > 20) {
                $error == true;
            }
            try {
                if (!$error) {
                    $user = new User($email, sha1($password), $username);
                    $pdo = new UserDao();
                    $result = $pdo->userExistForReg($user);
                    if (!$result) {
                        $pdo->registerUser($user);
                        $info = $pdo->getUserInfoByEmail($user);
                        $_SESSION['user'] = [];
                        $new = [
                            "id" => $info['user_id'],
                            "name" => $info['user_name'],
                            "reg_date" => $info['user_date'],
                            "image" => $info['user_pic'],
                            "cover" => $info['user_cover'],
                            "city" => $info['user_city'],
                            "description" => $info['user_description'],
                            "email" => $email,
                        ];
                        $_SESSION['user'] = $new;
                        header("Location: ./view/home.php");
                    } else {
                        header("location: ./index.php"); //User all ready EXIST!!
                    }
                } else {
                    header("location: ./index.php"); // ERRORS!
                }
            } catch (\PDOException $e) {
                $this->exception($e);
            }
        }
    } //Регистрация

    public function userById(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $id = htmlentities($_GET['id']);
                $user = new User(null, null, null, null, null, null, null, $id);
                $dao = new UserDao();
                $json = $dao->getUserInfoById($user);
                echo json_encode($json);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }

    }

    public function showSmallDiv(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
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
        }catch (\PDOException $e){
            $this->exception($e);
        }
    } // Показва допълнителна информация за посочения юзър от долната функция.

    public function showRandomUsers(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $dao = new UserDao();
            $email = $_SESSION['user']['email'];
            $random_users = $dao->getFourRandomUsers($email);
            echo json_encode($random_users);
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Показва 3 рандом юзъри. !!!!!!!!!!! --> СМЕНИ ЗАЯВКАТА ДА СЕ ПОКАЗВАТ САМО ТАКИВА КОИТО НЕ СИ ПОСЛЕДВАЛ !!!!!!

    public function showProfile(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = htmlentities($_GET['name']);
                if($name == $_SESSION['user']['name']){
                    $response = "my";
                    echo json_encode($response);
                }else{
                    $user = new User(null,null,$name);
                    $dao = new UserDao();
                    $info = $dao->getUserInfoByName($user);
                    if($info == null){
                        $response = "my";
                        echo json_encode($response);
                    }else{
                        echo json_encode($info);
                    }
                }
            }
        } catch (\PDOException $e){
            $this->exception($e);
        }
    } //Извлича нужната информация за попълване на профил.

    public function showOtherUserFollowings(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

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

    public function showMyProfile(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $name = $_SESSION['user']['name'];

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

    public function showMyFollowers(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            $id = $_SESSION['user']['id'];
            $user = new User(null,null,null,null,null,null,null,$id);
            $dao = new UserDao();
            $result = $dao->findFollowers($user);
            echo json_encode($result);

        }catch (\PDOException $e){
            $this->exception($e);
        }
    } //Показва моите последователи.

    public function showFollowings(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            $dao = new UserDao();
            $id = $_SESSION['user']['id'];
            $result = $dao->findFollowing($id);
            echo json_encode($result);
        }catch (\PDOException $e){
            $this->exception($e);
        }
    } //Изкарва информация за всички които следва.

    public function showFollowers(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $name = $_GET['name'];
                $dao = new UserDao();
                $id = $dao->findId($name);
                $user = new User(null,null,null,null,null,null,null,$id['user_id']);
                $result = $dao->findFollowers($user);

                echo json_encode($result);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Изкарва информация за всички последователи.

    public function searchUserAndTags(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $users = "";
                $name = htmlentities($_GET['name']);

                if (strlen($name) == 0) {
                    echo json_encode($users);
                } else {
                    $hashtags = [];
                    $dao = new UserDao();
                    $email = $_SESSION['user']['email'];
                    $users = $dao->getFirstFiveUsersByName($name, $email);
                    foreach ($users[0] as $twat) {
                        $words = explode(" ", $twat['twat_content']);
                        foreach ($words as $word) {
                            if (strstr($word, '#')) {
                                $hashtags[] = $word;
                            }
                        }
                    }
                    unset($users[0]);
                    $users[0] = $hashtags;
                    echo json_encode($users);
                }
            } catch (\PDOException $e) {
                $this->exception($e);
            }

        }
    } //Търси юзъри и тагове.

    public function profile(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            $logged_mail = $_SESSION["user"]['email'];
            $user = new User($logged_mail);
            $dao = new UserDao();
            $result = $dao->getUserInfoByEmail($user);
            echo json_encode($result);

        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Взима информация за юзъра по имейл.

    public function followUser(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $me = $_SESSION['user']['id'];
                $name = $_GET['name'];
                $dao = new UserDao();
                $you = $dao->findId($name);
                $like = $dao->likeIt($me, $you['user_id']);
                echo json_encode($like);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Последвай юзър.

    public function isFollow(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $my = $_SESSION['user']['id'];
                $name = htmlentities($_GET['name']);
                $dao = new UserDao();
                $id = $dao->findId($name);
                $result = $dao->isFollow($my,$id['user_id']);
                echo json_encode($result);
            }
        } catch (\PDOException $e) {
            $this->exception($e);
        }
    } //Проверява дали посетения юзър е вече последван или не (за бутона).

    public function getFFT(){
        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

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

    public function editProfile(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        if (isset($_POST['btn_edit'])) {
            try {
                $username = htmlentities($_POST['username']);
                $email = htmlentities($_POST['email']);
                $password = htmlentities($_POST['password']);
                $city = htmlentities($_POST['city']);
                $description = htmlentities($_POST['description']);
                $url_image = "assets/images/uploads/image_$email.png";
                $url_cover = "assets/images/uploads/cover_$email.png";
                $tmp_image = $_FILES['user_pic']['tmp_name'];
                $tmp_cover = $_FILES["user_cover"]["tmp_name"];

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
                $id = $_SESSION['user']['id'];
                $user = new User($email, sha1($password), $username, $url_image, $url_cover, $city, $description, $id);

                $pdo = new UserDao();
                $pdo->updateUser($user);
                header("location: ./view/profile.php");

            } catch (\PDOException $e) {
                $this->exception($e);
            }

        }

    } //Редактиране на профил.

    public function unfollowUser(){

        function __autoload($class)
        {
            $class = "..\\" . $class;
            require_once str_replace("\\", "/", $class) . ".php";
        }

        try{
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $me = $_SESSION['user']['id'];
                $name = $_GET['name'];
                $dao = new UserDao();
                $you = $dao->findId($name);
                $dislike = $dao->dislikeIt($me,$you['user_id']);
                echo json_encode($dislike);
            }
        }catch (\PDOException $e){
            $this->exception($e);
        }
    } //Отхаресване на юзър.

    public function logout(){
        if (isset($_SESSION['user'])){
            session_destroy();
            header('location: index.php');
        }
    } //Прекратяване на сесията и препращане към index.php.


}