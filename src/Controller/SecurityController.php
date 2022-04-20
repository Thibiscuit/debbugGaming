<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * page d'inscription
     * @Route("/registration", name="registrationPage")
     */
    public function newUser(Request $request, UserPasswordEncoderInterface $userEncoder): Response
    {
        $newUser = new User();
        $newUser->setRoles(['ROLE_USER'])
            ->setLocked(false)
            ->setAvatar('user_icon.png');
        $userForm = $this->createForm(RegistrationFormType::class, $newUser);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $hash = $userEncoder->encodePassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hash);

            $entityManager->persist($newUser);
            $entityManager->flush();
            return $this->redirectToRoute('connectionPage', []);
        }

        return $this->render('pages/inscription.html.twig', [
            'pageTitle' => 'Inscription',
            'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/login", name="connectionPage")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/connection.html.twig', [
            'pageTitle' => 'Connexion',
            'last_username' => $lastUsername,
            'error' => $error
            ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
