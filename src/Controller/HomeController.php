<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        //Ajouter un formulaire pour créer un nouveau dossier de chatons
        $form = $this->createFormBuilder() //je récupère un constructeur de formulaire
                ->add("dossier", TextType::class, ["label"=>"Nom du dossier à créer"])
                ->add("ok", SubmitType::class, ["label"=>"OK"])
                ->getForm();//je récupère une instance de formulaire créé par le builder à la fin

        //Gestion du retour en POST
        //1 : ajouter un paramètre Request à la méthode
        //Récupérer les données dans l'objet request
        $form->handleRequest($request);
        //Si le formulaire a été posté et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()){
            //lire les données
            $data=$form->getData();
            $dossier=$data["dossier"];

            //Traitement
            $fs=new Filesystem();
            $fs->mkdir("Photos/".$dossier);

            return $this->redirectToRoute("afficherDossier", ["nomDuDossier"=>$dossier]);
        }

        //Constituer le modèle à transmettre à la vue
        $finder=new Finder();
        $finder->directories()->in("Photos");

        //je transmets le modèle à la vue
        return $this->render('home/index.html.twig', [
            "dossiers"=>$finder,
            "formulaire"=>$form->createView()
        ]);
    }

    /**
     * @Route("/Photos/{nomDuDossier}", name="afficherDossier")
     */
    public function afficherDossier($nomDuDossier, Request $request): Response{

        //vérifier si le dossier existe
        $fs=new Filesystem();
        $chemin="Photos/".$nomDuDossier;
        //s'il n'existe pas, je lève une erreur 404
        if(!$fs->exists($chemin))
            throw $this->createNotFoundException("Le dossier $nomDuDossier n'existe pas");

        //Ajouter un formulaire permettant d'ajouter une photo
        //avec un champ de type FileType et un bouton OK
        //Création d'un formulaire
        $form=$this->createFormBuilder()
            ->add("photo", FileType::class, ['label'=>"Choisissez un joli chaton sur votre ordinateur"])
            ->add("ajouter", SubmitType::class, ["label"=>"Envoyer"])
            ->getForm();

        //Traitement du POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //on déplace le fichier uploadé au bon endroit
            $data["photo"]->move("Photos/" . $nomDuDossier
                , $data["photo"]->getClientOriginalName());

        }

        //Si j'arrive là, c'est que le dossier existe
        $finder=new Finder();
        $finder->files()->in($chemin);

        return $this->render('home/afficherDossier.html.twig', [
            "nomDuDossier"=>$nomDuDossier,
            "fichiers"=>$finder,
            "formulaire"=>$form->createView()
        ]);
    }
}
