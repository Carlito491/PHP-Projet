<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    //pas de route, ça renvoie une vue partielle
    public function _menu(): Response
    {
        //Constituer le modèle à transmettre à la vue
        $finder=new Finder();
        $finder->directories()->in("../public/Photos");//Constituer le modèle à transmettre à la vue

        return $this->render('menu/_menu.html.twig', [
            "dossiers"=>$finder
        ]);
    }
}
