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

namespace Teapot\Base\UserBundle\Controller;

use Teapot\Base\UserBundle\Form\UserSignupType;
use Teapot\Base\UserBundle\Entity\User;
use Teapot\Base\UserBundle\Entity\UserGroup;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller
{

    public function signupAction()
    {
        $request = $this->getRequest();

        $user = new User();

        $form = $this->createForm(new UserSignupType(), $user);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $em = $this->get('doctrine')->getManager();

                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                $user->setSlug();
                $user->setDateCreated(new \DateTime());

                $group = $em->getRepository('TeapotBaseUserBundle:UserGroup')
                            ->findOneBy(array('role' => 'ROLE_USER'));

                if (false === $group instanceof UserGroup) {
                    throw new \RuntimeException("No User Role");
                }

                $user->addGroup($group);

                $em->persist($user);
                $em->flush();

                $this->redirect("/");
            }
        }

        return $this->render('TeapotBaseUserBundle:Registration:signup.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function forgotAction($token)
    {

        return $this->render('TeapotBaseUserBundle:Registration:forgot.html.twig', array(

        ));

    }
}