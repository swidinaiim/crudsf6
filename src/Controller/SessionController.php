<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if($session ->has(name:'nbVisite')){
            $nb = $session->get(name:'nbVisite') + 1;
            $session->set('nbVisite',$nb);
        }else{
            $nb = 1;
        }
        $session->set('nbVisite',$nb);

        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
        ]);
    }
}
