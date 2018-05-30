<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 19/05/2018
 * Time: 17:26
 */

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


class ConsultController extends FOSRestController
{
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
        $idspecialty = $request->get("id_speciality");
        $file = $request->files->get('file');
        if (null === $description) {
            return $this->templateJson(404, "Parameter description is needed", 1, "");
        }
        if (null === $idspecialty) {
            return $this->templateJson(404, "Parameter id_specialty is needed", 1, "");
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

        $consultSpecialty = new ConsultaEspecialidad();
        $consultSpecialty->setIdConsulta($consult->getId());
        $consultSpecialty->setIdEspecialidad($idspecialty);
        $entityManager->persist($consultSpecialty);
        $entityManager->flush();

        if ($file != null) {
            $id = $consultSpecialty->getIdConsulta();

            foreach ($file as &$valor) {
                $this->crearAdjuntoConsulta($valor, $id);

            }
            return $this->templateJson(201, "Consult  with attachments created", 1, "")->setStatusCode(201);
        }
        return $this->templateJson(201, "Consult specialty created", 1, "")->setStatusCode(201);
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
            $id = $consultMedic->getIdConsulta();

            foreach ($file as &$valor) {
                $this->crearAdjuntoConsulta($valor, $id);

            }
            return $this->templateJson(201, "Consult  with attachments created", 1, count($file))->setStatusCode(201);
        }
        return $this->templateJson(201, "Consult medic created", 1, "")->setStatusCode(201);
    }

    private function crearAdjuntoConsulta(File $file, int $id)
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


    }

    /**
     * @Route("/api/patient/consults")
     */
    public function getConsults()
    {
        $conn = $this->getDoctrine()->getConnection();
        $sql = 'SELECT c.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_respuesta,r.fecha fecharespuesta,r.respuesta,r.leido FROM consultas c 
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
     * @Route("/api/medic/consultspatiens")
     */
    public function getConsultsPatiens()
    {
        $conn = $this->getDoctrine()->getConnection();
        $sql = 'SELECT c.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_respuesta,r.fecha fecharespuesta,r.respuesta,r.leido 
                FROM consultas c 
                inner join usuarios u on u.id =c.id_paciente 
                LEFT join consultas_medicos cm on cm.id_consulta=c.id 
                LEFT join respuestas_paciente_consulta rpc on rpc.id_consulta=c.id 
                LEFT join respuestas_medico_consulta rmc on rmc.id_consulta=c.id 
                left join usuarios u2 on u2.id=rpc.id_paciente 
                left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id from respuestas as r GROUP by r.id order by fecha desc) as r on rpc.id_respuesta=r.id or rmc.id_respuesta=r.id 
                where cm.id_medico=:id GROUP by c.id ORDER BY greatest (c.fecha ,ifnull( r.fecha,0)) desc';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchAll())));
    }

    /**
     * @Route("/api/medic/consultspecialty")
     */
    public function getConsultsSpecialty()
    {
        $conn = $this->getDoctrine()->getConnection();
        $sql = 'SELECT c.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_respuesta,r.fecha fecharespuesta,r.respuesta,r.leido   	FROM consultas c 
				inner join usuarios u on u.id =c.id_paciente
                LEFT join consultas_especialidades ce on ce.id_consulta=c.id
                LEFT join respuestas_paciente_consulta rpc on rpc.id_consulta=c.id 
                LEFT join respuestas_medico_consulta rmc on rmc.id_consulta=c.id
                left join usuarios u2 on u2.id=rmc.id_medico
				inner join medicos m on m.id_especialidad=ce.id_especialidad
                 left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id from respuestas 					
                 as r GROUP by r.id  order by fecha desc)  as r
                on rpc.id_respuesta=r.id or rmc.id_respuesta=r.id
				 where ce.id_especialidad=:p1
                 GROUP by c.id  
                ORDER BY greatest (c.fecha  ,ifnull( r.fecha,0)) desc';
        $stmt = $conn->prepare($sql);
        $repository = $this->getDoctrine()->getRepository(Medico::class);

        /** @var $medic Medico */
        $medic = $repository->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);

        $stmt->execute(['p1' => $medic->getEspecialidad()]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchAll())));
    }

    /**
     * @Route("/api/patient/consults/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConsult($id)
    {
        $consult = $this->getDoctrine()->getManager()->getRepository(Consulta::class)->findOneBy(['id' => $id, 'idPaciente' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT a.* from adjuntos a inner join adjuntos_consultas ac on ac.id_adjunto=a.id inner join consultas c on c.id=ac.id_Consulta where ac.id_consulta=:p1 and c.id_paciente=:p2 ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $id, 'p2' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $sql2 = "SELECT r.*,Concat(u.nombre,' ',u.apellido) nombre from respuestas r
            left join respuestas_paciente_consulta rpc on r.id=rpc.id_respuesta 
            left join respuestas_medico_consulta rmc on r.id=rmc.id_respuesta 

            left join usuarios u on u.id=rmc.id_medico
            left join consultas c on c.id=rmc.id_consulta and c.id=rpc.id_consulta
            where rpc.id_consulta=:p1 or rmc.id_consulta=:p2
            order by r.fecha desc ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['p1' => $id, 'p2' => $id]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => array("Consult" => $consult, "Attachments" => $stmt->fetchall(), "Respuestas" => $stmt2->fetchAll()))));
    }
    /**
     * @Route("/api/medic/consults/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConsultMedic($id)
    {  /** @var $consult Consulta */
        $consult = $this->getDoctrine()->getManager()->getRepository(Consulta::class)->findOneBy(['id' => $id]);
        $conn = $this->getDoctrine()->getConnection();
        $sql3 ="SELECT c.*,Concat(u.nombre,' ',u.apellido) nombre from consultas c inner join usuarios u on u.id=c.id_paciente where c.id=:p1";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->execute(['p1' => $id]);

        $sql = "SELECT a.* from adjuntos a inner join adjuntos_consultas ac on ac.id_adjunto=a.id inner join consultas c on c.id=ac.id_Consulta where ac.id_consulta=:p1 and c.id_paciente=:p2 ";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $id, 'p2' => $consult->getIdPaciente()]);
        $sql2 = "SELECT r.*,Concat(u.nombre,' ',u.apellido) nombre from respuestas r
                left join respuestas_paciente_consulta rpc on r.id=rpc.id_respuesta 
                left join respuestas_medico_consulta rmc on r.id=rmc.id_respuesta 
                left join usuarios u on u.id=rmc.id_medico
                left join consultas c on c.id=rmc.id_consulta and c.id=rpc.id_consulta
                where rpc.id_consulta=:p1 or rmc.id_consulta=:p2
                order by r.fecha desc ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['p1' => $id, 'p2' => $id]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => array("Consult" => $stmt3->fetch(), "Attachments" => $stmt->fetchall(), "Respuestas" => $stmt2->fetchAll()))));
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