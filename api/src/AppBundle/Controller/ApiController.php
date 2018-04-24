<?php

// src/AppBundle/Controller/ApiController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Cita;
use AppBundle\Entity\EnfermedadesCronicas;
use AppBundle\Entity\Especialidad;
use AppBundle\Entity\Historial;
use AppBundle\Entity\User;
use DateTime;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiController extends FOSRestController
{
    /**
     * @Route("/api/patient/user")
     */
    public function getUser()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MEDICO')) {
            return $this->handleView($this->view(array("status" => 200,"message"=>"","type" => 2, "data" => $this->get('security.token_storage')->getToken()->getUser())));
            //throw new AccessDeniedException();
        } else {
            $conn = $this->getDoctrine()->getConnection();
            $sql = 'SELECT u.id,u.nombre,u.apellido,u.email,u.direccion,u.fecha_nacimiento,u.telefono,u.movil,u.pais_nacimiento,u.sexo,u.estado_civil,u.ocupacion,u.notas,u.foto,CONCAT(u2.nombre," " ,u2.apellido)as nombre_medico, u2.id as id_medico FROM usuarios u inner join pacientes p on u.id=p.id_usuario inner join usuarios u2 on p.id_medico=u2.id WHERE u.id=:id';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
            return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" => $stmt->fetch())));        }

    }

    /**
     * @Route("/api/patient/historical/{id}", name="show_historical")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getHistorical($id)
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT h.id,h.causa,h.notas,h.fecha,CONCAT(u.nombre," " ,u.apellido)as nombre_medico FROM historial h inner join pacientes p on h.id_paciente=p.id_usuario inner join usuarios u on h.id_medico=u.id WHERE p.id_usuario=:id and h.id=:idhistorical';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId(),'idhistorical' => $id]);
        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" => $stmt->fetch())));

    }
    /**
     * @Route("/api/patient/historical")
     */
    public function getHistoricals()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT h.id,h.causa,h.notas,h.fecha,CONCAT(u.nombre," " ,u.apellido)as nombre_medico FROM historial h inner join pacientes p on h.id_paciente=p.id_usuario inner join usuarios u on h.id_medico=u.id WHERE p.id_usuario=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" => $stmt->fetchall())));

    }
    /**
     * @Route("/api/patient/recipes")
     */
    public function getRecipes()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT r.* FROM recetas r inner join historial h on r.id_historial=h.id inner join pacientes p on h.id_paciente=p.id_usuario WHERE p.id_usuario=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        if ($stmt->fetchall()==null) {
            return $this->handleView($this->view(array("status" => 404,"message"=>"No hay recetas", "type" => 1, "data" =>[])));

        }

        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" =>$stmt->fetchall())));

    }


    /**
     * @Route("/api/pacient/getchronic")
     */
    public function getChronic()
    {
        $id=$this->get('security.token_storage')->getToken()->getUser()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $chronic = $entityManager->getRepository(EnfermedadesCronicas::class)->findBy(['idPaciente' => $id]);

        if (!$chronic) {
            return $this->handleView($this->view(array("status" => 404,"message"=>"No existe", "type" => 1, "data" =>[])));

        }

        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" =>$chronic)));

    }

    /**
     * @Route("/api/patient/citations")
     */
    public function getCitations()
    {
        $id=$this->get('security.token_storage')->getToken()->getUser()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $chronic = $entityManager->getRepository(Cita::class)->findBy(['idPaciente' => $id]);

        if (!$chronic) {
            return $this->handleView($this->view(array("status" => 404,"message"=>"No existe", "type" => 1, "data" =>[])));

        }

        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" =>$chronic)));

    }

    /**
     * @Route("/api/pacient/prueba22")
     */
    public function indexAction2()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT u.id,u.nombre,u.apellido,u.email,u.direccion,u.fecha_nacimiento,u.telefono,u.movil,u.pais_nacimiento,u.sexo,u.estado_civil,u.ocupacion,u.notas,u.foto,CONCAT(u2.nombre," " ,u2.apellido)as nombre_medico, u2.id as id_medico FROM usuarios u inner join pacientes p on u.id=p.id_usuario inner join usuarios u2 on p.id_medico=u2.id WHERE u.id=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        return $this->handleView($this->view(array("status" => 200,"message"=>"", "type" => 1, "data" => $stmt->fetchall())));

        // returns an array of arrays (i.e. a raw data set)

    }

    /**
     * @Route("/admin/prueba3")
     */
    public function indexAction4()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $data = $this->get('security.token_storage')->getToken();
            $view = $this->view($data);
            return $this->handleView($view);

        } else {
            $data = array("hello" => "world");
            $view = $this->view($data);
            return $this->handleView($view);
        }

    }

    /**
     * @Route("/api/paciente/especialidades")
     */
    public function getEspecialidades()
    {
        $repository = $this->getDoctrine()->getRepository(Especialidad::class);
        return $this->handleView($this->view(array("data" => array("total" => sizeof($repository->findAll()), "especialidades" => $repository->findAll()))));
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
        $user->setPlainPassword("1234");
        $user->setUsername("12345678G");
        $user->setUsernameCanonical("12345678G");
        $user->setNombre("test");
        $user->setApellido("test");
        $user->setSuperAdmin(true);
        $user->setDireccion("test");
        $user->setFechaNacimiento((new \DateTime)->setDate(1995, 06, 16));
        $user->setTelefono(123456789);
        $user->setMovil(61626261);
        $user->setPais("ESPAÑA");
        $user->setSexo("Hombre");
        $user->setEmail("test23");
        $user->setEmailCanonical("test23");
        $user->setEnabled(true);
        $user->setRoles((array('ROLE_PACIENTE')));
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