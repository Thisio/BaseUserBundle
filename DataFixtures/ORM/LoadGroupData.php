<?php

namespace Teapotio\Base\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Teapotio\Base\UserBundle\Entity\UserGroup;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $adminGroup = new UserGroup();
        $adminGroup->setName('Admin');
        $adminGroup->setRole('ROLE_ADMIN');

        $manager->persist($adminGroup);

        $userGroup = new UserGroup();
        $userGroup->setName('User');
        $userGroup->setRole('ROLE_USER');

        $manager->persist($userGroup);

        $manager->flush();

        $this->addReference('role-admin', $adminGroup);
        $this->addReference('role-user', $userGroup);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 100;
    }
}