<?php
/**
 * Created by PhpStorm.
 * User: gamig
 * Date: 4/18/2018
 * Time: 12:13 PM
 */

namespace model;


class CommentDao extends BaseDao
{

    public function addComment(Comment $comment)
    {
        $statement = $this->pdo->prepare("INSERT INTO comments (twat_id, comment_text, owner_id) VALUES (?,?,?)");
        $statement->execute(array($comment->getTweetId(),
            $comment->getContent(),
            $comment->getOwnerId()));
        $result = $statement->rowCount();

        return $result;
    } // Добавя коментар

    public function showMyComments($id)
    {
        $statement = $this->pdo->prepare("SELECT c.twat_id, c.comment_text, c.comment_date, u.user_pic, u.user_name FROM comments AS c JOIN users AS u ON u.user_id = c.owner_id WHERE c.twat_id = ?");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Показва коментарите под туитовете

    public function findTweetOwner($id)
    {
        $statement = $this->pdo->prepare("SELECT user_id AS id FROM twats WHERE twat_id = ?");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    } // Намира автора на даден туит
}