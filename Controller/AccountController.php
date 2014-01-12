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

use Teapot\Base\UserBundle\Form\ForgotPasswordType;
use Teapot\Base\UserBundle\Form\ResetPasswordType;

use Teapot\Base\UserBundle\Entity\User;
use Teapot\Base\UserBundle\Entity\UserGroup;
use Teapot\Base\UserBundle\Entity\UserToken;

use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{

    public function forgotPasswordAction()
    {
        list($form, $isTokenGenerated) = $this->resetPasswordForm();

        return $this->render('TeapotBaseUserBundle:Account:forgotPassword.html.twig', array(
            'form'                 => $form->createView(),
            'is_token_generated'   => $isTokenGenerated
        ));
    }

    protected function resetPasswordForm()
    {
        $request = $this->get('request');

        $error = $this->get('session')->getFlashBag()->get('error');

        $customErrorKey = null;

        if (empty($error) === false) {
            $customErrorKey = $error[0];
        }

        $user = new User();

        $isTokenGenerated = false;

        $form = $this->createFormBuilder()
            ->add('username', 'text')
            ->add('email', 'text')
            ->getForm();

        if ($request->getMethod() === "POST") {
            $form->bind($request);

            $em = $this->get('doctrine')
                       ->getEntityManager();

            $user = $this->get('teapot.user')
                         ->getByUsernameAndEmail($form['username']->getData(), $form['email']->getData());

            if ($user instanceof User) {

                $userToken = $this->get('teapot.user')
                                  ->getTokenByUser($user);

                $generateToken = true;
                if (true === $userToken instanceof UserToken) {
                    $now = new \DateTime();

                    // Valid only 2 hours
                    if ($userToken->getDateCreated()->add(new \DateInterval('PT2H')) < $now) {
                        $em->remove($userToken);
                        $em->flush();
                    }
                    else {
                        // The token is still valid
                        $generateToken = false;
                        $customErrorKey = "Token.still.valid";
                    }
                }

                if ($generateToken === true) {
                    $userToken = $this->get('teapot.user')->createUserToken();
                    $userToken->setUser($user);
                    $userToken->setDateCreated(new \DateTime());
                    $userToken->setToken(sha1(uniqid(mt_rand(), true)));

                    $em->persist($userToken);
                    $em->flush();

                    $isTokenGenerated = true;
                }
            }
            else {
                $form->addError(new FormError($this->get('translator')->trans('No.record.found')));
            }
        }

        if ($customErrorKey !== null) {
            $form->addError(new FormError($this->get('translator')->trans($customErrorKey)));
        }

        return array($form, $isTokenGenerated);
    }

    public function resetPasswordAction()
    {
        $request = $this->get('request');
        $token = $request->query->get('token');

        if ($token !== null) {
            $em = $this->get('doctrine')
                       ->getEntityManager();

            $userToken = $this->get('teapot.user')
                              ->getToken($token);

            if (true === $userToken instanceof UserToken) {

                $user = $userToken->getUser();
                $form = $this->createForm(new ResetPasswordType(), $user);

                $now = new \DateTime();

                // Handle password reset
                if ($request->getMethod() === "POST") {
                    $form->bind($request);
                    if ($form->isValid() === true) {

                        $factory = $this->container->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($user);
                        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                        $user->setPassword($password);

                        $em->persist($user);

                        $em->remove($userToken);
                        $em->flush();

                        return $this->render('TeapotBaseUserBundle:Account:resetPasswordSuccessful.html.twig', array(
                            'form' => $form->createView()
                        ));
                    }
                }

                // Valid only 2 hours
                if ($userToken->getDateCreated()->add(new \DateInterval('PT2H')) < $now) {
                    $this->get('session')->getFlashBag()->set('error', 'Token.is.expired');

                    return $this->redirect(
                        $this->generateUrl('TeapotBaseUserBundle_forgotPassword')
                    );
                }
                else {
                    return $this->render('TeapotBaseUserBundle:Account:resetPassword.html.twig', array(
                        'form' => $form->createView()
                    ));
                }
            }
            else {
                $this->get('session')->getFlashBag()->set('error', 'Token.unexisting');

                return $this->redirect(
                    $this->generateUrl('TeapotBaseUserBundle_forgotPassword')
                );
            }
        }

        return $this->redirect(
            $this->generateUrl('TeapotBaseUserBundle_forgotPassword')
        );
    }
}