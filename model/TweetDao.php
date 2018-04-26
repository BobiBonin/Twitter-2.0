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
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, t.twat_date, t.twat_content, t.twat_id FROM twats AS t JOIN users AS u ON u.user_id = t.user_id WHERE u.user_id = ? ORDER BY t.twat_date DESC");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function showUserTweets($id)
    {
        $statement = $this->pdo->prepare("SELECT twat_content,twat_date,user_id FROM twats WHERE user_id=? ORDER BY twat_date DESC");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        print_r(json_encode($result));
    }

    public function addTweet(Tweet $tweet)
    {
        $statement = $this->pdo->prepare("INSERT INTO twats (user_id,twat_content)  VALUES (?,?)");
        $statement->execute(array($tweet->getUserId(), $tweet->getContent()));
    }

    public function getMyFollowersTweets($str)
    {
        $statement = $this->pdo->query("
                                  SELECT users.user_name,users.user_pic ,twats.twat_content, twats.twat_date, twats.user_id, twats.twat_id 
                                  FROM users,twats 
                                  WHERE ($str) 
                                  AND twats.user_id = users.user_id 
                                  ORDER BY twats.twat_date DESC");
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function likeATweet($twat_id,$user_id){
        $statement = $this->pdo->prepare("INSERT INTO likes (user_id, twat_id) VALUES (?,?)");
        $statement->execute(array($user_id,$twat_id));
    }

    public function getHashtags($hashtag){
        $statement = $this->pdo->prepare("SELECT u.user_name, u.user_pic, t.twat_date, t.twat_content, t.twat_id FROM twats AS t JOIN users AS u ON u.user_id = t.user_id WHERE t.twat_content LIKE ? ");
        $statement->execute(array("%#".$hashtag."%"));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }


}