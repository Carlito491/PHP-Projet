<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatonController extends AbstractController
{
    #[Route('/chaton', name: 'app_chaton')]
    public function index(): Response
    {
        return $this->render('chaton/index.html.twig', [
            'controller_name' => 'ChatonController',
        ]);
    }
}
