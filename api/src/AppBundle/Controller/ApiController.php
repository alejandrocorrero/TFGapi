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
use AppBundle\Entity\User;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use function PHPSTORM_META\type;
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
     * @Route("/api/patient/user")
     */
    public function getUser()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MEDICO')) {
            return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 2, "data" => $this->get('security.token_storage')->getToken()->getUser())));
            //throw new AccessDeniedException();
        } else {
            $conn = $this->getDoctrine()->getConnection();
            $sql = 'SELECT u.id,u.nombre,u.apellido,u.email,u.direccion,u.fecha_nacimiento,u.telefono,u.movil,u.pais_nacimiento,u.sexo,u.estado_civil,u.ocupacion,u.notas,u.foto,CONCAT(u2.nombre," " ,u2.apellido)as nombre_medico, u2.id as id_medico FROM usuarios u inner join pacientes p on u.id=p.id_usuario inner join usuarios u2 on p.id_medico=u2.id WHERE u.id=:id';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
            return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetch())));
        }

    }

    /**
     * @Route("/api/patient/historical/{id}", name="show_historical")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getHistorical($id)
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT h.id,h.causa,h.diagnostico,h.notas,h.fecha,CONCAT(u.nombre," " ,u.apellido)as nombre_medico FROM historial h inner join pacientes p on h.id_paciente=p.id_usuario inner join usuarios u on h.id_medico=u.id WHERE p.id_usuario=:id and h.id=:idhistorical';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId(), 'idhistorical' => $id]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetch())));

    }

    /**
     * @Route("/api/patient/historical")
     */
    public function getHistoricals()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT h.id,h.causa,h.diagnostico,h.notas,h.fecha,CONCAT(u.nombre," " ,u.apellido)as nombre_medico, c.nombre as centro_salud,c.direccion as direccion_centro FROM historial h inner join pacientes p on h.id_paciente=p.id_usuario inner join usuarios u on h.id_medico=u.id inner join medicos m on m.id_usuario=u.id inner join centros c on c.id=m.id_centro WHERE p.id_usuario=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchall())));

    }

    /**
     * @Route("/api/patient/recipes")
     */
    public function getRecipes()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT r.* FROM recetas r inner join historial h on r.id_historial=h.id inner join pacientes p on h.id_paciente=p.id_usuario WHERE p.id_usuario=:id and DATE_ADD(r.fecha, INTERVAL r.duracion DAY)>CURRENT_DATE';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $result = $stmt->fetchall();
        if ($result == null) {
            return $this->handleView($this->view(array("status" => 404, "message" => "No hay recetas", "type" => 1, "data" => [])));

        }

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $result)));

    }

    /**
     * @Route("/api/patient/recipeshistorical/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRecipesHistorical($id)
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT r.* FROM recetas r inner join historial h on r.id_historial=h.id inner join pacientes p on h.id_paciente=p.id_usuario WHERE p.id_usuario=:id HAVING r.id_historial=:id_historial';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId(), 'id_historial' => $id]);
        $array = $stmt->fetchall();
        if ($array == null) {
            return $this->templateJson(404, "No hay recetas", 1, "")->setStatusCode(404);

        }


        return $this->templateJson(200, "", 1, $array)->setStatusCode(200);

    }


    /**
     * @Route("/api/patient/chronic")
     */
    public function getChronic()
    {
        $id = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $chronic = $entityManager->getRepository(EnfermedadesCronicas::class)->findBy(['idPaciente' => $id]);

        if (!$chronic) {
            return $this->handleView($this->view(array("status" => 404, "message" => "No existe", "type" => 1, "data" => [])));

        }

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $chronic)));

    }

    /**
     * @Route("/api/patient/citations")
     */
    public function getCitations()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT c.*,CONCAT(u.nombre," " ,u.apellido)as nombre_medico, ce.nombre as centro FROM citas c join usuarios u on c.id_medico= u.id  join medicos m on m.id_usuario=c.id_medico JOIN centros ce on ce.id = m.id_centro WHERE c.dia>=CURRENT_DATE and c.id_paciente=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchall())));

    }

    /**
     * @Route("/api/patient/citationsmedic")
     */
    public function getCitationsMedic()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT c.dia,c.hora FROM citas c WHERE c.id_medico=:id';
        $stmt = $conn->prepare($sql);
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $entityManager->getRepository(Paciente::class)->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        $stmt->execute(['id' => $patient->getIdMedico()]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchall())));

    }

    /**
     * @Route("/api/patient/medichorary")
     */
    public function getMedicHorary()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT h.dia,h.hora_inicio,h.hora_fin FROM horarios h WHERE h.id_medico=:id';
        $stmt = $conn->prepare($sql);
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $entityManager->getRepository(Paciente::class)->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $stmt->execute(['id' => $patient->getIdMedico()]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchall())));

    }

    /**
     * @Route("/api/patient/specialties")
     */
    public function getSpecialties()
    {
        $repository = $this->getDoctrine()->getRepository(Especialidad::class);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $repository->findAll())));
    }

    /**
     * @Route("/api/patient/create_consult_specialties")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setConsultSpecialty(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $description = $request->get("description");
        $idspeciality = $request->get("id_speciality");
        $attached = $request->get("attached");
        if (null === $description) {
            return $this->templateJson(404, "Parameter id_specialty is needed", 1, "");
        }
        if (null === $idspeciality) {
            return $this->templateJson(404, "Parameter id_specialty is needed", 1, "");
        }
        $consult = new Consulta();
        $consult->setDescripcion($description);
        $consult->setFecha(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $consult->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());
        $entityManager->persist($consult);
        $entityManager->flush();

        $consultSpecialty = new ConsultaEspecialidad();
        $consultSpecialty->setIdConsulta($consult->getId());
        $consultSpecialty->setIdEspecialidad($idspeciality);
        $entityManager->persist($consultSpecialty);
        $entityManager->flush();

        return $this->templateJson(201, "Created", 1, $consult->getId())->setStatusCode(201);
    }

    /**
     * @Route("/api/patient/create_consult_medic")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setConsultMedic(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $description = $request->get("description");
        $idmedic = $request->get("id_medic");
        $file = $request->files->get('file');
        if (null === $description) {
            return $this->templateJson(404, "Parameter description is needed", 1, "");
        }
        if (null === $idmedic) {
            return $this->templateJson(404, "Parameter id_medic is needed", 1, "");
        }
        if ($file != null) {
            $tipos = array("jpeg", "jpg", "png", "tiff", "bmp", "raw", "nef");
            foreach ($file as &$valor) {
                if (!in_array($valor->guessExtension(), $tipos)) {
                    return $this->templateJson(404, "El archivo tiene que ser de tipo imagen", 1, "")->setStatusCode(404);
                }
            }
        }
        $consult = new Consulta();
        $consult->setDescripcion($description);
        $consult->setFecha(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $consult->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());

        $entityManager->persist($consult);
        $entityManager->flush();

        $consultMedic = new ConsultaMedico();
        $consultMedic->setIdConsulta($consult->getId());
        $consultMedic->setIdMedico($idmedic);
        $entityManager->persist($consultMedic);
        $entityManager->flush();
        if ($file != null) {
            $id=$consultMedic->getIdConsulta();

            foreach ($file as &$valor) {
               return $this->crearAdjuntoConsulta($valor,$id);

            }
        }
        return $this->templateJson(201, "Consult medic created", 1,"")->setStatusCode(201);
    }

    private function crearAdjuntoConsulta(File $file,int $id)
    {
        $adjunto = new Adjunto();
        $adjuntoconsulta = new AdjuntoConsulta();

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('adjuntos_directory'), $fileName);
        $adjunto->setPath($fileName);
        $adjunto->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        $adjunto->setTamanio($file->getClientSize());
        $adjunto->setNombre("Archivo consulta");
        $em = $this->getDoctrine()->getManager();
        $em->persist($adjunto);
        $em->flush();
        $adjuntoconsulta->setIdAdjunto($adjunto->getId());
        $adjuntoconsulta->setIdConsulta($id);
        $em->persist($adjuntoconsulta);
        $em->flush();
        return  $this->templateJson(201, "Consult medic with attachments created", 1,"")->setStatusCode(201);


    }

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
     * @Route("/api/patient/create_citation")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setCitation(Request $request)
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $day = $request->get("day");
            $time = $request->get("time");
            if (null === $day) {
                return $this->templateJson(404, "Parameter day is needed", 1, "");
            }
            if (null === $time) {
                return $this->templateJson(404, "Parameter time is needed", 1, "");
            }
            $citation = new Cita();
            $citation->setIdMedico($this->getDoctrine()->getManager()->getRepository(Paciente::class)->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()])->getIdMedico());
            $citation->setDia(DateTime::createFromFormat("d/m/Y", $day));
            $citation->setHora(DateTime::createFromFormat("H:i:s", $time));
            $citation->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());
            $entityManager->persist($citation);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->templateJson(404, "Cita ocupada", 1, "")->setStatusCode(200);
        }
        return $this->templateJson(201, "Created", 1, "")->setStatusCode(201);
    }

    /**
     * @Route("/api/patient/consults")
     */
    public function getConsultsMedic()
    {
        $conn = $this->getDoctrine()->getConnection();
        $entityManager = $this->getDoctrine()->getManager();
        $sql = 'SELECT c.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_medico,r.fecha fecharespuesta,r.respuesta,r.leido FROM consultas c 
                inner join usuarios u on u.id =c.id_paciente
                LEFT join consultas_medicos cm on cm.id_consulta=c.id 
                LEFT join consultas_especialidades ce on ce.id_consulta=c.id  
                LEFT join respuestas_paciente_consulta rpc on rpc.id_consulta=c.id 
                LEFT join respuestas_medico_consulta rmc on rmc.id_consulta=c.id
                left join usuarios u2 on u2.id=rmc.id_medico
                left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id from respuestas as r GROUP by r.id  order by fecha desc)  as r
                on rpc.id_respuesta=r.id or rmc.id_respuesta=r.id
                where c.id_paciente=:id
                GROUP by c.id  
                ORDER BY greatest (c.fecha  ,ifnull( r.fecha,0)) desc';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchAll())));
    }

    /**
     * @Route("/api/patient/attachments")
     */
    public function getAdjuntos()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT a.* FROM adjuntos_pacientes ap inner join adjuntos a on ap.id_adjunto=a.id where ap.id_paciente=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchAll())));
    }

    /**
     * @Route("/api/patient/consults_specialty")
     */
    public function getConsultsSpeacialty()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT c.*,ce.id_especialidad FROM consultas c inner join consultas_especialidades ce on c.id=ce.id_consulta WHERE c.id_paciente=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchAll())));
    }

    /**
     * @Route("/api/patient/consults_medic/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConsultMedic($id)
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT c.*,cm.id_medico FROM consultas c inner join consultas_medicos cm on c.id=cm.id_consulta WHERE c.id_paciente=:id and c.id=:id_consult';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId(), 'id_consult' => $id]);
        $sql2 = 'SELECT r.*,CONCAT(p.nombre ," ", p.apellido) as paciente_nombre,CONCAT(m.nombre ," ", m.apellido) as medico_nombre from respuestas r LEFT join respuestas_paciente_consulta rpc on rpc.id_respuesta=r.id LEFT join respuestas_medico_consulta rmc on rmc.id_respuesta=r.id LEFT join usuarios m on rmc.id_medico=m.id LEFT join usuarios p on rpc.id_paciente=p.id where rmc.id_consulta=:id_consulta or rpc.id_consulta=:id_consulta order by r.fecha';
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['id_consulta' => $id]);
        return $this->templateJson(200, "", 1, array("consult" => $stmt->fetch(), "responses" => $stmt2->fetchAll()))->setStatusCode(200);
    }


    /**
     * @Route("/api/patient/consults_specialty/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConsultSpecialty($id)
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'SELECT c.*,ce.id_especialidad FROM consultas c inner join consultas_especialidades ce on c.id=ce.id_consulta WHERE c.id_paciente=:id and c.id=:id_consult';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId(), 'id_consult' => $id]);
        $sql2 = 'SELECT r.*,CONCAT(p.nombre ," ", p.apellido) as paciente_nombre,CONCAT(m.nombre ," ", m.apellido) as medico_nombre from respuestas r LEFT join respuestas_paciente_consulta rpc on rpc.id_respuesta=r.id LEFT join respuestas_medico_consulta rmc on rmc.id_respuesta=r.id LEFT join usuarios m on rmc.id_medico=m.id LEFT join usuarios p on rpc.id_paciente=p.id where rmc.id_consulta=:id_consulta or rpc.id_consulta=:id_consulta order by r.fecha';
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['id_consulta' => $id]);
        return $this->templateJson(200, "", 1, array("consult" => $stmt->fetch(), "responses" => $stmt2->fetchAll()))->setStatusCode(200);
    }

    // TODO ARREGLAR

    /**
     * @Route("/api/medic/create_econsult")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setEConsult(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $description = $request->get("description");
        $idmedic = $request->get("id_medic");
        $attached = $request->get("attached");
        if (null === $description) {
            return $this->templateJson(404, "Parameter id_specialty is needed", 1, "");
        }
        if (null === $idmedic) {
            return $this->templateJson(404, "Parameter id_medic is needed", 1, "");
        }
        $consult = new Consulta();
        $consult->setDescripcion($description);
        $consult->setFecha(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $consult->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());

        $entityManager->persist($consult);
        $entityManager->flush();

        $consultMedic = new ConsultaMedico();
        $consultMedic->setIdConsulta($consult->getId());
        $consultMedic->setIdMedico($idmedic);
        $entityManager->persist($consultMedic);
        $entityManager->flush();

        return $this->templateJson(201, "Created", 1, $consult->getId())->setStatusCode(201);
    }

    //TODO AÑADIR ARRAYD E ARCHIVOS A CONSULTA

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
     * @Route("/api/patient/file/{file}")
     * @param $file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download($file)
    {
        return $this->file(__DIR__ . '/../../../web/uploads/adjuntos/' . $file);
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
     * @Route("/api/patient/specialties")
     */
    public function getEspecialidades()
    {
        $repository = $this->getDoctrine()->getRepository(Especialidad::class);
        return $this->templateJson(200, "", 1, $repository->findAll());
        // return $this->handleView($this->view(array("data" => array("total" => sizeof($repository->findAll()), "especialidades" => $repository->findAll()))));
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