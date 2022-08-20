<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/personne')]

class PersonneController extends AbstractController
{

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine):Response
    {
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


    #[Route('/add', name: 'app_personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();
        $personne = new Personne();
        $personne->setFirstname(firstname:'Jadjoud');
        $personne->setName(name:'Douda');
        $personne->setAge(age:'2');

        //Ajouter l'opération d'insertion de la personne dans ma transaction
        $entityManager->persist($personne);

        // Execute la transaction
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
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