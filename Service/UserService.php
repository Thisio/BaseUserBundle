<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapot
 * @package    BaseUserBundle
 * @author     Thomas Potaire
 */

namespace Teapot\Base\UserBundle\Service;

use Teapot\Base\UserBundle\Entity\User;
use Teapot\Base\UserBundle\Entity\UserGroup;
use Teapot\Base\UserBundle\Entity\UserToken;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection;

class UserService extends BaseService {

    public function createUser()
    {
        return new User();
    }

    public function createUserToken()
    {
        return new UserToken();
    }

    /**
     * Whether a user is a super admin or not
     *
     * @param  UserInterface  $user = null
     *
     * @return boolean
     */
    public function isSuperAdmin(UserInterface $user = null)
    {
        // Don't bother doing the rest.
        if ($user === null) {
            return false;
        }

        foreach ($user->getGroups() as $group) {
            if ($group->getRole() === UserGroup::ROLE_SUPER_ADMIN) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a user is an admin
     *
     * @param  UserInterface  $user = null
     *
     * @return boolean
     */
    public function isAdmin(UserInterface $user = null)
    {
        // Don't bother doing the rest.
        if ($user === null) {
            return false;
        }

        // If the user is a super admin then return true directly
        if ($this->isSuperAdmin($user) === true) {
            return true;
        }

        foreach ($user->getGroups() as $group) {
            if ($group->getRole() === UserGroup::ROLE_ADMIN) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a single entity or null
     *
     * @param   int        $id
     * @param   boolean    $deleted = false
     * @return  User|null
     */
    public function find($id, $deleted = false)
    {
        return $this->em
                    ->getRepository($this->userRepositoryClass)
                    ->find($id, $deleted);
    }

    /**
     * Get a collection of Users by user ids
     *
     * @param  array  $ids
     *
     * @return ArrayCollection
     */
    public function getByIds($ids)
    {
        return $this->em
                    ->getRepository($this->userRepositoryClass)
                    ->getByIds($ids);
    }

    public function getByUsernameAndEmail($username, $email)
    {
        return $this->em
                    ->getRepository($this->userRepositoryClass)
                    ->findOneBy(array(
                        'username' => $username,
                        'email'    => $email,
                    ));
    }

    public function getTokenByUser(UserInterface $user)
    {
        return $this->em
                    ->getRepository($this->userTokenRepositoryClass)
                    ->findOneBy(array('user' => $user));
    }

    public function getToken($token)
    {
        return $this->em
                    ->getRepository($this->userTokenRepositoryClass)
                    ->findOneBy(array('token' => $token));
    }

}