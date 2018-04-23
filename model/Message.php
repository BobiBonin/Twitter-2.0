<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 22.4.2018 г.
 * Time: 12:17
 */

namespace model;


class Message implements \JsonSerializable
{

    private $text;
    private $receiverId;
    private $ownerId;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function __construct($ownerId,$receiverId,$text)
    {
        $this->text = $text;
        $this->ownerId = $ownerId;
        $this->receiverId = $receiverId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getReceiverId()
    {
        return $this->receiverId;
    }

    /**
     * @param mixed $receiverId
     */
    public function setReceiverId($receiverId)
    {
        $this->receiverId = $receiverId;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param mixed $ownerId
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }



}