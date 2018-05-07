<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/17/2018
 * Time: 10:26 AM
 */

namespace model;

use model\User;
use MongoDB\BSON\ObjectId;

class UserDao extends BaseDao
{

    /*Проверява дали юзъра съществува за LOGIN*/
    public function checkUserExist(User $user)
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as rows FROM users WHERE user_email = ? AND user_pass = ?");
        $statement->execute(array($user->getEmail(), $user->getPassword()));
        $result = $statement->fetch();
        return $result['rows'] > 0;
    }

    /*Взима информация за текущо логнатия юзър по имейл*/
    public function getUserInfoByEmail(User $user)
    {
        $statement = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_date, user_pic, user_cover, user_city, user_description FROM users WHERE user_email = ?");
        $statement->execute(array($user->getEmail()));
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $user->setId($result['user_id']);
        $user->setUsername($result['user_name']);
        $user->setDate($result['user_date']);
        $user->setImageUrl($result['user_pic']);
        $user->setCoverUrl($result['user_cover']);
        $user->setCity($result['user_city']);
        $user->setDescription($result['user_description']);
        return $user;

    }

    /*Проверява дали юзъра съществува по имейл за регистрацията*/
    public function userExistForReg(User $user)
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as rows FROM users WHERE user_email = ?");
        $statement->execute(array($user->getEmail()));
        $result = $statement->fetch();
        return $result['rows'] > 0;
    }

    /*Регистрира ЮЗЪР*/
    public function registerUser(User $user)
    {
        $statement = $this->pdo->prepare("INSERT INTO users (user_name,user_email,user_pass, user_pic, user_cover, user_date) VALUES (?,?,?,?,?,?)");
        $statement->execute(array($user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getImageUrl(),
            $user->getCoverUrl(),
            $user->getDate()
        ));
        $result = $this->pdo->lastInsertId();
        $user->setId($result);
        return $user;
    }

    /*Търси първите 5 потребителя по име (за търсачката)*/
    public function getFirstFiveUsersByName($name, $email)
    {
        $result = [];
        $statement = $this->pdo->prepare("SELECT twat_content FROM twats WHERE twat_content LIKE ? LIMIT 5");
        $statement->execute(array("%#" . $name . "%"));
        $result[] = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $statement = $this->pdo->prepare("SELECT user_name, user_pic FROM users WHERE user_name LIKE ? AND NOT user_email = ? LIMIT 5");
        $statement->execute(array($name . "%", $email));
        $result[] = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    /*Намира броя на следващите дадения потребител юзъри по ИМЕ*/
    public function getUserFollowings(User $user)
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as num FROM users as u JOIN following as f ON u.user_id = f.user_id WHERE u.user_name = ?");
        $statement->execute(array($user->getUsername()));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Намира броя на последваните потребители по ИМЕ*/
    public function getUserFollowers(User $user)
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as num FROM following as f JOIN users as u ON u.user_id = f.following_id WHERE u.user_name = ?");
        $statement->execute(array($user->getUsername()));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Намира броя на публикуваните туитове по ИМЕ на потребителя*/
    public function getUserTwits(User $user)
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as num FROM twats as t  JOIN users as u  ON u.user_id = t.user_id WHERE u.user_name = ?");
        $statement->execute(array($user->getUsername()));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Georgi -- 23.03.2018 -- Търси конкретен юзър по име*/
    public function getUserInfoByName(User $user)
    {
        $statement = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_date, user_pic, user_cover, user_city, user_description FROM users WHERE user_name = ?");
        $statement->execute(array($user->getUsername()));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Georgi -- 23.03.2018 -- Ъпдейтва профила*/
    public function updateUser(User $user)
    {
        $statement = $this->pdo->prepare("UPDATE users SET user_name = ?,user_email = ?,user_pass = ?,user_pic = ?,user_cover = ?, user_city = ?, user_description = ? WHERE user_id = ?");
        $statement->execute(array($user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getImageUrl(),
            $user->getCoverUrl(),
            $user->getCity(),
            $user->getDescription(),
            $user->getId()
        ));
    }

    /*Взима инфо за юзъра по ID*/
    public function getUserInfoById(User $user)
    {
        $statement = $this->pdo->prepare("SELECT user_id, user_name, user_email, user_date, user_pic, user_cover, user_city, user_description FROM users WHERE user_id = ?");
        $statement->execute(array($user->getId()));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Намира следените от дадения потребител юзъри по ID*/
    public function findFollowers($id)
    {
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, u.user_cover FROM users as u JOIN following as f ON u.user_id = f.user_id WHERE f.following_id = ?");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Georgi -- 23.03.2018 -- Намира ID на юзър по име*/
    public function findId($name)
    {
        $statement = $this->pdo->prepare("SELECT user_id FROM users WHERE user_name = ?");
        $statement->execute(array($name));
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Последван ли е юзъра*/
    public function isFollow($myid, $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM following WHERE user_id = ? AND following_id = ?");
        $statement->execute(array($myid, $id));
        $result = $statement->rowCount();
        return $result;
    }

    /*Взима following ID*/
    public function getFollowersId($user_id)
    {
        $statement = $this->pdo->prepare("SELECT following_id from mydb.following WHERE user_id = ?");
        $statement->execute(array($user_id));
        $result = $statement->fetchAll(\PDO::FETCH_COLUMN);
        return $result;
    }

    /*Отхаресва потребител*/
    public function dislikeIt($me, $you)
    {
        $statement = $this->pdo->prepare("DELETE FROM following WHERE user_id = ? AND following_id = ?");
        $statement->execute(array($me, $you));
        $result = $statement->rowCount();
        return $result;
    }

    /*Харесва потребител*/
    public function likeIt($me, $you, $message, $status)
    {
        $statement = $this->pdo->prepare("INSERT INTO following (user_id,following_id) VALUES (?,?)");
        $statement->execute(array($me, $you));
        $result = $statement->rowCount();

        $statement = $this->pdo->prepare("INSERT INTO notifications (sender, receiver, message, status) VALUES (?,?,?,?)");
        $statement->execute(array($me, $you, $message, $status));

        return $result;
    }

    /*Намира следващите дадения потребител юзъри по ID*/
    public function findFollowing($id)
    {
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, u.user_cover, u.user_city, u.user_description FROM users as u JOIN following as f ON u.user_id = f.following_id WHERE f.user_id = ?");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Избира произволни 3 юзъра*/
    public function getFourRandomUsers($email)
    {
        $statement = $this->pdo->prepare("SELECT user_name, user_pic FROM users  WHERE user_id < (SELECT COUNT(*) FROM users) AND NOT user_email = ?  ORDER BY RAND()  LIMIT 3 ");
        $statement->execute(array($email));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Взима айдито на юзъра с име*/
    public function getUserIdFromName($userName)
    {
        $statement = $this->pdo->prepare("SELECT user_id FROM users WHERE user_name = ?");
        $statement->execute(array($userName));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Взима известията за юзър по айди*/
    public function getNotifications($id)
    {
        $statement = $this->pdo->prepare("SELECT id,sender,id_tweet,message,user_pic, date, user_name, status 
                                                    FROM notifications  as n
                                                    JOIN users as u
                                                    ON n.sender = u.user_id
                                                    WHERE n.receiver = ? 
                                                    ORDER BY date DESC"
        );
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /*Променя статуса на известията от непрочетени на прочетени*/
    public function seeNotification($id)
    {
        $statement = $this->pdo->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
        $statement->execute(array($id));
    }

    /*Записва известията в пазата при определени ситуации(последване, коментиране, харесване на пост или отбелязване на юзър)*/
    public function sendNotification($sender, $receiver, $tweetId, $message, $status){
        $statement = $this->pdo->prepare("INSERT INTO notifications (sender, receiver, id_tweet, message, status) VALUES (?,?,?,?,?)");
        $statement->execute(array($sender, $receiver, $tweetId, $message, $status));
    }

}