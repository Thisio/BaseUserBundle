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

namespace Teapot\Base\UserBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\NoResultException;

use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository implements UserProviderInterface
{

    /**
     * Returns a single entity or null
     *
     * @param   int        $id
     * @param   boolean    $deleted = false
     *
     * @return  User|null
     */
    public function find($id, $deleted = false)
    {
        $queryBuilder = $this->createQueryBuilder('u')
                             ->select(array('u'))
                             ->where('u.id = :id')->setParameter('id', $id);

        if ($deleted !== null) {
            $queryBuilder->andWhere('u.isDeleted = :isDeleted')
                         ->setParameter('isDeleted', $deleted);
        }

        $query = $queryBuilder->getQuery();

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            return null;
        }
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
        $queryBuilder = $this->createQueryBuilder('u')
                             ->select(array('u'));

        $queryBuilder->where($queryBuilder->expr()->in('u.id', $ids));

        return new ArrayCollection($query->getResult());
    }

    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('Unable to find an active admin AcmeUserBundle:User object identified by "%s".', $username), null, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}