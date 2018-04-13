<?php

// src/AppBundle/Controller/ApiController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiController extends FOSRestController
{
    /**
     * @Route("/api/{id}")
     */
    public function indexAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setRoles( array('ROLE_ADMIN') );
        $entityManager->flush();
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @Route("/prueba")
     */
    public function indexAction2()
    {
        //$repository = $this->getDoctrine()->getRepository(User::class)->findAll();
        $user = $this->get('security.token_storage')->getToken();
        $view = $this->view($user);
        return $this->handleView($view);
        // Do something with the fully authenticated user.
        // ...
    }

    /**
     * @Route("/prueba3")
     */
    public function indexAction3()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        } else {
            $data = array("hello" => "world");
            $view = $this->view($data);
            return $this->handleView($view);
        }

    }
    /**
     * @Route("/paciente/prueba3")
     */
    public function indexAction4()
    {

            $data = array("hello" => "world");
            $view = $this->view($data);
            return $this->handleView($view);


    }
}