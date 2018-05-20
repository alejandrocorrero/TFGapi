<?php

// src/AppBundle/Controller/ApiController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Adjunto;
use AppBundle\Entity\AdjuntoConsulta;
use AppBundle\Entity\AdjuntoPaciente;
use AppBundle\Entity\Cita;
use AppBundle\Entity\Consulta;
use AppBundle\Entity\ConsultaEspecialidad;
use AppBundle\Entity\ConsultaMedico;
use AppBundle\Entity\EnfermedadesCronicas;
use AppBundle\Entity\Especialidad;
use AppBundle\Entity\Historial;
use AppBundle\Entity\Medico;
use AppBundle\Entity\Paciente;
use AppBundle\Entity\Respuesta;
use AppBundle\Entity\RespuestaPacienteConsulta;
use AppBundle\Entity\User;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Date;

class ApiController extends FOSRestController
{

















    /**
     * @Route("api/patient/adjunto/new", name="app_adjunto_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public
    function new(Request $request)
    {
        $product = new Adjunto();
        $adjuntop = new AdjuntoPaciente();
        $tipos = array("jpeg", "jpg", "png", "tiff", "bmp", "raw", "nef");
        $file = $request->files->get('file');
        $name = $request->get('name');
        if ($file == null) {
            return $this->templateJson(400, "El archivo no puede estar vacio", 1, "")->setStatusCode(400);
        }
        if (!in_array($file->guessExtension(), $tipos)) {
            return $this->templateJson(404, "El archivo tiene que ser de tipo imagen", 1, "")->setStatusCode(404);
        }
        if ($name == null) {
            return $this->templateJson(404, "El nombre no puede estar vacio", 1, "")->setStatusCode(404);
        }

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('adjuntos_directory'), $fileName);
        $product->setPath($fileName);
        $product->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        $product->setTamanio($file->getClientSize());
        $product->setNombre($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $adjuntop->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());
        $adjuntop->setIdAdjunto($product->getId());
        $em->persist($adjuntop);
        $em->flush();
        /*$adjuntoc->setIdAdjunto($product->getId());
        $adjuntoc->setIdConsultas(6);
        $em->persist($adjuntoc);
        $em->flush();*/
        return $this->templateJson(201, "Creado", 1, $product)->setStatusCode(201);


    }















    /**
     * @Route("api/patient/foto", name="app_foto_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newFoto(Request $request)
    {
        $product = $this->get('security.token_storage')->getToken()->getUser();

        $tipos = array("jpeg", "jpg", "png", "tiff", "bmp", "raw", "nef");
        $file = $request->files->get('file');
        if ($file == null) {
            return $this->templateJson(400, "El archivo no puede estar vacio", 1, "")->setStatusCode(400);
        }
        if (!in_array($file->guessExtension(), $tipos)) {
            return $this->templateJson(404, "El archivo tiene que ser de tipo imagen", 1, "")->setStatusCode(404);
        }

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('adjuntos_directory'), $fileName);
        $product->setPath($fileName);
        //$product->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        //$product->setTamanio($file->getClientSize());
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return $this->templateJson(201, "Creado", 1, $product)->setStatusCode(201);


    }


    /**
     * @Route("/api/patient/logout")
     */
    public function indexAction4()
    {
        $data = $this->get('security.token_storage')->setToken(null);
        $this->get('security.token_storage')->getToken()->invalidate();
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $data =
            $view = $this->view($data);
            return $this->handleView($view);

        } else {
            $data = array("hello" => "world");
            $view = $this->view($data);
            return $this->handleView($view);
        }

    }

    /**
     * @Route("/api/patient/test")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexj(Request $request)
    {
        $file = $request->files->get('file');
        $name = $request->get('name');
        if ($name == null) {
            return $this->templateJson(400, "El archivo no puede estar vacio", 1, "")->setStatusCode(400);
        } else {
            return $this->templateJson(400, $file[0], 1, "")->setStatusCode(400);

        }

    }



    /**
     * @Route("/admin/medico")
     */
    public function medico()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $user = new user();
        $user->setPlainPassword("1234");
        $user->setUsername("12345678M");
        $user->setUsernameCanonical("12345678m");
        $user->setNombre("Medico1");
        $user->setApellido("Medico2");
        $user->setSuperAdmin(true);
        $user->setDireccion("test");
        $user->setFechaNacimiento((new \DateTime)->setDate(1995, 06, 16));
        $user->setTelefono(123456789);
        $user->setMovil(61626261);
        $user->setPais("ESPAÑA");
        $user->setSexo("Hombre");
        $user->setEmail("medico1");
        $user->setEmailCanonical("medico2");
        $user->setEstadoCivil("soltero");
        $user->setOcupacion("Medico");
        $user->setNotas("naada");
        $user->setPath("path");
        $user->setEnabled(true);
        $user->setRoles((array('ROLE_MEDICO')));
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);


        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        $medico = new Medico();
        $medico->setId($user->getId());
        $medico->setCentro(1);
        $medico->setEspecialidad(6);
        $entityManager->persist($medico);
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

    /**
     * @param $status
     * @param $message
     * @param $type
     * @param $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function templateJson($status, $message, $type, $data)
    {
        return $this->handleView($this->view(array("status" => $status, "message" => $message, "type" => $type, "data" => $data)));
    }
}