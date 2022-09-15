<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        //Constituer le modèle à transmettre à la vue
        $finder=new Finder();
        $finder->directories()->in("../public/Photos");

        //je transmets le modèle à la vue
        return $this->render('home/index.html.twig', [
            "dossiers"=>$finder
        ]);
    }

    /**
     * @Route("/voir/{nomDuDossier}", name="afficherDossier")
     */
    public function afficherDossier($nomDuDossier): Response{
        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier"=>$nomDuDossier
        ]);
    }
}
