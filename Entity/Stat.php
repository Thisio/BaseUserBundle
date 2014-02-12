<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    BaseUserBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\Base\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teapotio\Base\UserBundle\Entity\Stat
 *
 * @ORM\Table(name="`user_stat`")
 * @ORM\Entity()
 */
class Stat
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $users
     *
     * @ORM\Column(name="users", type="integer")
     */
    protected $users = 0;

    /**
     * @var \DateTime $dateModified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $dateModified;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set users
     *
     * @param integer $users
     * @return BoardStat
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return integer
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return BoardStat
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Convenient methods
     */

    /**
     * Get a short value of the number of users
     *
     * @return string
     */
    public function getShortUsers()
    {
        return $this->convertFormat($this->users);
    }

    /**
     * Convert an integer into a short string
     *
     * 1000 -> 1K
     * 1500 -> 1.5K
     * 150500 -> 150K
     * 1000000 -> 1M
     * 1500000 -> 1.5M
     *
     * @param  integer  $var
     * @return string
     */
    protected function convertFormat($var)
    {
        if ($var < 1000) {
            return $var;
        }
        else if ($var < 10000) {
            return round($var / 1000, 1) ."K";
        }
        else if ($var < 1000000) {
            return round($var / 1000) ."K";
        }
        else if ($var < 1000000) {
            return round($var / 1000000) . "M";
        }
    }
}
