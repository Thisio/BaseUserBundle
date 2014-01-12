TeapotBaseUserBundle
====================

Work-in-progress ~ Unstable

User Bundle for Symfony2 that provides a simple User structure.

Bundle has DataFixtures, if you want to use them:
[1] In AppKernel.php, in $bundles add "new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),"
[2] php app/console doctrine:fixtures:load
/!\ It will create an admin account, don't forget to replace it with your own account.
More about Fixtures here: http://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html