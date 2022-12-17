<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        //Ajouter un formulaire pour créer un nouveau dossier de chatons
        $form = $this->createFormBuilder() //je récupère un constructeur de formulaire
        ->add("proprio", TextType::class, ["label"=>"Nom du propriétaire"])
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
            $proprio=$data["proprio"];

            //Traitement
            $fs=new Filesystem();
            $fs->mkdir("Proprietaires/".$proprio);

            return $this->redirectToRoute("afficherProprio", ["nomDuProprio"=>$proprio]);
        }

        //Constituer le modèle à transmettre à la vue
        $finder=new Finder();
        $finder->directories()->in("Proprietaires");

        //je transmets le modèle à la vue
        return $this->render('home/index.html.twig', [
            "proprios"=>$finder,
            "formulaire2"=>$form->createView()
        ]);
    }




    /**
     * @Route("/Proprietaires/{nomDuProprio}", name="afficherProprio")
     */
    public function afficherProprio($nomDuProprio, Request $request): Response{

        //vérifier si le dossier existe
        $fs=new Filesystem();
        $chemin="Proprietaires/".$nomDuProprio;
        //s'il n'existe pas, je lève une erreur 404
        if(!$fs->exists($chemin))
            throw $this->createNotFoundException("Le propriétaire $nomDuProprio n'existe pas");

        //Ajouter un formulaire permettant d'ajouter une photo
        //avec un champ de type FileType et un bouton OK
        //Création d'un formulaire
        $form=$this->createFormBuilder()
            ->add("proprietaire", FileType::class, ['label'=>"Choisissez un propriétaire sur votre ordinateur"])
            ->add("ajouter", SubmitType::class, ["label"=>"Envoyer"])
            ->getForm();

        //Traitement du POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //on déplace le fichier uploadé au bon endroit
            $data["proprietaire"]->move("Proprietaires/" . $nomDuProprio
                , $data["proprietaire"]->getClientOriginalName());

        }

        //Si j'arrive là, c'est que le dossier existe
        $finder=new Finder();
        $finder->files()->in($chemin);

        return $this->render('home/afficherProprio.html.twig', [
            "nomDuProprio"=>$nomDuProprio,
            "fichiers"=>$finder,
            "formulaire2"=>$form->createView()
        ]);
    }
}