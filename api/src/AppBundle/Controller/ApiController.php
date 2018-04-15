<?php

// src/AppBundle/Controller/ApiController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use DateTime;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiController extends FOSRestController
{
    /**
     * @Route("/admin/{id}")
     */
    public function indexAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setRoles( array('ROLE_PACIENTE') );
        $entityManager->flush();
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/paciente/prueba22")
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
     * @Route("/admin/prueba3")
     */
    public function indexAction4()
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
     * @Route("/admin/test")
     */
    public function index()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $user = new user();
        $user->setPlainPassword("adminpassword");
        $user->setUsername("75918243G");
        $user->setUsernameCanonical("75918243g");
        $user->setNombre("test");
        $user->setApellido("test");
        $user->setSuperAdmin(true);
        $user->setDireccion("test");
        $user->setFechaNacimiento( (new \DateTime)->setDate( 1995 ,06 , 16 ));
        $user->setTelefono(123456789);
        $user->setMovil(61626261);
        $user->setPais("ESPAÑA");
        $user->setSexo("Hombre");
        $user->setEmail("test");
        $user->setEmailCanonical("test");
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        $data = array("Medico" => $user->getNombre());
        $view = $this->view($data);
        return $this->handleView($view);
    }

    /**
     * @Route("/prueba", methods="POST")
     */
    public function edit($id)
    {
        // ... edit a post
    }
}