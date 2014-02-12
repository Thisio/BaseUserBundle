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

namespace Teapotio\Base\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\NoResultException;

use Doctrine\Common\Collections\ArrayCollection;

class UserGroupRepository extends EntityRepository
{

}