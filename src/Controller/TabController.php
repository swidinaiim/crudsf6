<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{
    #[Route('/tab/{nb<\d+>?5}', name: 'tab')]
    public function index($nb): Response
    {
        $notes = [];
        for($i = 0 ; $i < $nb ; $i++){
            $notes[] = rand(0, 20);
        }
        return $this->render('tab/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/tab/users', name: 'tab.users')]
    public function users(): Response
    {

        $users = [
            [ 'firstname' => 'Jadjoud', 'lastname' => 'Manai' , 'age' => '2' ],
            [ 'firstname' => 'Chaima', 'lastname' => 'Swidi' , 'age' => '28' ],
            [ 'firstname' => 'Naiim', 'lastname' => 'Swidi' , 'age' => '37']

        ];

        return $this->render('tab/users.html.twig', [
            'users' => $users,
        ]);
    }

}
