<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * UserFriends table with 2 primary keys: $id, $friend=$id
 *
 * @ORM\Table(name="user_friends")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserFriendsRepository")
 */
class UserFriends
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Id
     * //targetEntity=ClassName, //inversedBy=ClassName.variable
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     * @ORM\JoinColumn(name="app_users_id", referencedColumnName="id")
     * //name=ORM\TableColumnName, //referencedColumnName="id" (MyProduct.my_category_id -> MyCategory.id)
     */
    private $friend;

    /**
     * @var bool
     * @ORM\Column(name="is_confirmed", type="boolean")
     *
     */
    private $isConfirmed;


    /**
     * @var string
     * @ORM\Column(name="msg", type="string", length=100, nullable=true )
     *
     */
    private $msg;

    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * set $isConfirmed to false
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->isConfirmed = false;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @param mixed $friend
     */
    public function setFriend($friend)
    {
        $this->friend = $friend;
    }


    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @param boolean $isConfirmed
     *
     * @return UserFriends
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }
    /**
     * @return mixed
     */

    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function __toString()
    {
        return (string) $this->getFriend();
    }
}

