<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/18/2018
 * Time: 11:10 AM
 */

namespace model;


class TweetDao extends BaseDao
{

    public function showMyTweets($id)
    {
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, t.twat_date, t.twat_content, t.twat_id, t.twat_img FROM twats AS t JOIN users AS u ON u.user_id = t.user_id WHERE u.user_id = ? ORDER BY t.twat_date DESC");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Показва туитовете на логнатия потребител

    public function showUserTweets($id)
    {
        $statement = $this->pdo->prepare("SELECT twat_content,twat_date,user_id FROM twats WHERE user_id=? ORDER BY twat_date DESC");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        print_r(json_encode($result));
    } // Показва туитовете на други потребители

    public function addTweet(Tweet $tweet)
    {
        $statement = $this->pdo->prepare("INSERT INTO twats (user_id,twat_content,twat_img)  VALUES (?,?,?)");
        $statement->execute(array($tweet->getUserId(), $tweet->getContent(), $tweet->getImage()));
        $result = $this->pdo->lastInsertId();
        return $result;
    } //Добавя нов туит

    public function getMyFollowersTweets($arr)
    {
        $user_id = $_SESSION['user']->getId();
        $string = "twats.user_id = " . $user_id . " OR ";
        for ($i = 0; $i < count($arr); $i++) {
            $string = $string . "twats.user_id = $arr[$i]";
            if ($i < count($arr) - 1) {
                $string = $string . " OR ";
            }
        }


        $statement = $this->pdo->query("SELECT users.user_name,users.user_pic ,twats.twat_content, twats.twat_date, twats.user_id, twats.twat_id,twats.twat_img
                                  FROM users,twats 
                                  WHERE ($string) 
                                  AND twats.user_id = users.user_id 
                                  ORDER BY twats.twat_date DESC");
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }// Показва туитовете на юзърите които е последвал логнатия потребител

    public function likeATweet($tweet_id, $user_id)
    {
        $statement = $this->pdo->prepare("INSERT INTO likes (user_id, twat_id) VALUES (?,?)");
        $statement->execute(array($user_id, $tweet_id));

    } // Харесва туит

    public function dislikeATweet($tweet_id, $user_id)
    {
        $statement = $this->pdo->prepare("DELETE FROM `mydb`.`likes` WHERE `user_id`=? and`twat_id`=?;");
        $statement->execute(array($user_id, $tweet_id));
    }// Отхаресва туит

    public function getHashtags($hashtag)
    {
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, t.twat_date, t.twat_content, t.twat_id, t.twat_img FROM twats AS t JOIN users AS u ON u.user_id = t.user_id WHERE t.twat_content LIKE ? ");
        $statement->execute(array("%#" . $hashtag . "%"));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Извлича хаштаговете

    public function getNewId()
    {
        $statement = $this->pdo->prepare("SELECT twat_id FROM mydb.twats ORDER BY twat_id DESC LIMIT 1;");
        $statement->execute(array());
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Взима ново айди (за снимка)

    public function getTweetLikes($id)
    {
        $statement = $this->pdo->prepare("SELECT count(*) as likes FROM likes where twat_id = ?");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }// Извлича броя на лайковете за даден туит

    public function checkIfLiked($userId, $tweetId)
    {
        $statement = $this->pdo->prepare("SELECT count(*) as is_liked FROM mydb.likes where user_id = ? AND twat_id = ?");
        $statement->execute(array($userId, $tweetId));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Проверява дали даден туит е лайкнат

    public function getTweet($tweetId)
    {
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, t.twat_date, t.twat_content, t.twat_id, t.twat_img FROM twats AS t JOIN users AS u ON u.user_id = t.user_id WHERE t.twat_id = ?");
        $statement->execute(array($tweetId));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }// Взима определени туитове

    public function deleteTweet($id)
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare("DELETE FROM likes WHERE twat_id = ?");
            $statement->execute(array($id));
            $statement = $this->pdo->prepare("DELETE FROM comments WHERE twat_id = ?");
            $statement->execute(array($id));
            $statement = $this->pdo->prepare("DELETE FROM twats WHERE twat_id = ?");
            $statement->execute(array($id));

            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    } // Изтрива туит заедно с неговите коментари и лайкове
}