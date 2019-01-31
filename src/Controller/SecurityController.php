<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    /**
     * @Route("/deconnexion", name="app_logout")
     */
    public function logout(){}

    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request)
    {
        $user = new User();
        // si le form est soumis

        //création formulaire en l'association au user
        $userForm= $this->createForm(RegisterType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted()&&
            $userForm->isValid()){

            //hashe le mot de passe
            $password = $user->getPassword();
            $hash = $encoder->encodePassword($user, $password);
            $user ->setPassword($hash);

            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($user);
            $em->flush();

            //si besoin, envoyer un email ici

            // crée un flash message
            $this->addFlash('success', 'Bravo, vous êtes inscrits !');

            //redirige vers la page de détails de cette question
            return $this->redirectToRoute('home');

        }

        return $this->render('default/register.html.twig', [
            "userForm"=> $userForm->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}
