<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('default/home.html.twig');
        //return new Response('pifpaf');
    }

    /**
     * @Route("/faq", name="faq")
     * URL et nom de la route
     */
    public function  faq()
    {
        return $this->render('default/faq.html.twig');
    }

    /**
     * @Route("/CGU", name="CGU")
     *
     */
    public function CGU()
    {
        return $this->render('default/CGU.html.twig');
    }

    /**
     * @Route("/fonctionnement", name="fonctionnement")
     *
     */
    public function fonctionnement()
    {
        return $this->render('default/fonctionnement.html.twig');
    }

    /**
     * @Route("/monCompte", name="monCompte")
     *
     */
    public function infoCompte()
    {
        return $this->render('default/monCompte.html.twig');
    }

}
