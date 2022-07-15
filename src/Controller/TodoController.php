<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// préfixe
#[Route("/todo")]

class TodoController extends AbstractController
{

    /**
     * @Route("/", name="app_todo")
     */
    public function index( Request $request): Response
    {
        $session = $request->getSession();

        if(!$session->has(name:'todos')){

            $todos = [
                'achat' => 'acheter clé usb',
                'cours' => 'finaliser mon cours',
                'correction' => 'corriger mes examens'
            ];

            $session->set('todos',$todos);
            $this->addFlash(type:'info', message:"LA liste des todos vient d'etre initialisée ");

        }

        return $this->render(view:'todo/index.html.twig');
    }

    // ADD
    #[Route('/add/{name?test}/{content?test}', name: 'todo.add')]
    public function addTodo (Request $request, $name, $content): Response
    {

        $session = $request->getSession();
        
        if($session->has(name:'todos')){

            $todos = $session->get(name:'todos');

            if(isset($todos[$name])){
                $this->addFlash(type:'error', message:"Le todo $name existe déjà");

            }else{

                $todos[$name] = $content;
                $this->addFlash(type:'success', message:"Le todo $name a été ajouté avec succée");
                $session->set('todos',$todos);
            }

        }else{
            $this->addFlash(type:'error', message:"LA liste des todos n'est pas encore initialiser ");

        }
        return $this->redirectToRoute(route:'app_todo');

    }
    //UPDATE
    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo (Request $request, $name, $content): Response
    {

        $session = $request->getSession();
        
        if($session->has(name:'todos')){

            $todos = $session->get(name:'todos');

            if(!isset($todos[$name])){
                $this->addFlash(type:'error', message:"Le todo $name n'existe pas");

            }else{

                $todos[$name] = $content;
                $this->addFlash(type:'success', message:"Le todo $name a été modifié avec succée");
                $session->set('todos',$todos);
            }

        }else{
            $this->addFlash(type:'error', message:"LA liste des todos n'est pas encore initialiser ");

        }
        return $this->redirectToRoute(route:'app_todo');

    }
    //DELETE
    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo (Request $request, $name):Response 
    {

        $session = $request->getSession();
        
        if($session->has(name:'todos')){

            $todos = $session->get(name:'todos');

            if(!isset($todos[$name])){
                $this->addFlash(type:'error', message:"Le todo $name n'existe pas");

            }else{

                unset($todos[$name]);
                $this->addFlash(type:'success', message:"Le todo $name a été supprimée avec succée");
                $session->set('todos',$todos);
            }

        }else{
            $this->addFlash(type:'error', message:"LA liste des todos n'est pas encore initialiser ");

        }
        return $this->redirectToRoute(route:'app_todo');

    }

    //RESET
    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo (Request $request): Response
    {

        $session = $request->getSession();
        $session->remove(name:'todos');
        return $this->redirectToRoute(route:'app_todo');

    }

}
