<?php

namespace App\Controller;

use App\Form\PersonneType;
use App\Entity\Personne;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne')]

class PersonneController extends AbstractController
{
     public function __construct(private LoggerInterface $logger, private Helpers $helper)
     {
     }

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine):Response
    {
        echo $this->helper->SayCc();
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $personnes = $repository->findAll();
        
        return $this->render('personne/index.html.twig',[
            'personnes' => $personnes,
        ]);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax):Response
    {
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        
        return $this->render('personne/index.html.twig',['personnes' => $personnes]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.stats.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax):Response
    {
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        
        return $this->render('personne/stats.html.twig',['stats' => $stats[0], 'ageMin'=>$ageMin, 'ageMax'=>$ageMax]);
    }

    #[Route('/alls/{page?1}/{nbre?15}', name: 'personne.list.alls')]
    public function alls(ManagerRegistry $doctrine, $page, $nbre):Response
    {
        $repository = $doctrine->getRepository(persistentObject:Personne::class);
        $nbPersonne = $repository->count([]); 
        $nbPage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([],['id' => 'ASC'],limit:$nbre, offset:($page-1) * $nbre);
        
        return $this->render('personne/index.html.twig',[
            'personnes' => $personnes, 
            'isPaginated' => true,
            'nbPage' => $nbPage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null):Response
    {

        if(!$personne){
            $this->addFlash(
               'error',
               'La personne n\'existe pas'
            );
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/detail.html.twig',['personne' => $personne,]);
    }


    /*#[Route('/add', name: 'app_personne')]
    public function addPersonne(ManagerRegistry $doctrine, Request $request): Response
    {

        // $personne et l'image de notre formulaire
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        // Mon formulaire va aller traiter la requete
        $form->handleRequest($request);
        //Est ce que le formulaire a été soumis
        
        if ($form->isSubmitted()) { 
        //Si oui 
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            //on va l'ajouter dans la base de donnée
            $manager->flush();
            // Afficher un message de succées
            $this->addFlash(
               'success',
               '$personne->getName() a été ajouté avec succées'
            );
            // Redireger vers la liste des personne
            return   $this->redirectToRoute('personne.list.alls');

        }else{
            // Sinon on affiche seulement le formulaire

            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView(),
            ]);

        }
        
    }*/



    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(
        Personne $personne = null, 
        ManagerRegistry $doctrine, 
        Request $request, 
        UploaderService $uploaderService,
        MailerService $mailer
        ): Response
    {
        $new = false;
        if(!$personne){
            $new = true;
            // $personne et l'image de notre formulaire
            $personne = new Personne();        
        }

        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        // Mon formulaire va aller traiter la requete
        $form->handleRequest($request);
        //Est ce que le formulaire a été soumis
        
        if ($form->isSubmitted() && $form->isValid()) { 
        //Si oui 

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personne_directory');

                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }


            $manager = $doctrine->getManager();
            $manager->persist($personne);
            //on va l'ajouter dans la base de donnée
            $manager->flush();
            // Afficher un message de succées
            if($new){
                $message = 'a été ajouté avec succées';
            }else{
                $message = 'a été mis à jour avec succées';
            }

            $mailMessage = $personne->getFirstname().' '.$personne->getName().' '.$message;
            $mailer->sendEmail(content: $mailMessage);
            $this->addFlash(
               'success',
               $personne->getName().' '.$message
            );
            // Redireger vers la liste des personne
            return   $this->redirectToRoute('personne.list.alls');

        }else{
            // Sinon on affiche seulement le formulaire

            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView(),
            ]);

        }
        
    }



    #[Route('/delete/{id<\d+>}', name: 'personne.delete')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if($personne){
            $manager = $doctrine ->getManager();
            $manager->remove($personne);
            // Execute transaction
            $manager->flush();
            // Message de succés
            $this->addFlash('success', 'La personne a été supprimé avec succés');
        }else{
            $this->addFlash('error', 'personne innexistante');
        }
        return   $this->redirectToRoute('personne.list.alls');


    }
    #[Route('/update/{id<\d+>}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null,$name,$firstname,$age, ManagerRegistry $doctrine): Response
    {
        // Vérifier si la personne à mettre à jour existe 
        if($personne){

            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash(
               'success',
               'La personnea été mis à jou avec succées'
            );
            
        }else{
            $this->addFlash(
               'error',
               'Personne innexistante'
            );
        }
         return $this->redirectToRoute('personne.list.alls');
    }
    
}