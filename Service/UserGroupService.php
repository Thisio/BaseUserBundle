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

use Teapot\Base\UserBundle\Entity\UserGroup;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Doctrine\Common\Collections\ArrayCollection;

class UserGroupService extends BaseService {

    public function createUserGroup()
    {
        return new UserGroup();
    }

    /**
     * Save a group
     *
     * @param  RoleInterface $group
     *
     * @return RoleInterface
     */
    public function save(RoleInterface $group)
    {
        $this->em->persist($group);
        $this->em->flush();

        return $group;
    }

    public function getAllGroups()
    {
        return new ArrayCollection($this->em->getRepository($this->userGroupRepositoryClass)->findAll());
    }

    public function getById($id)
    {
        return $this->em->getRepository($this->userGroupRepositoryClass)->find($id);
    }
}