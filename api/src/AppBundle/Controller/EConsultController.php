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
use AppBundle\Entity\AdjuntoEconsulta;
use AppBundle\Entity\AdjuntoPaciente;
use AppBundle\Entity\Cita;
use AppBundle\Entity\Consulta;
use AppBundle\Entity\ConsultaEspecialidad;
use AppBundle\Entity\ConsultaMedico;
use AppBundle\Entity\Econsulta;
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


class EConsultController extends FOSRestController
{
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
        $idspecialty = $request->get("id_specialty");
        $idPatient = $request->get("id_patient");
        $file = $request->files->get('file');
        if (null === $description) {
            return $this->templateJson(404, "Parameter description is needed", 1, "");
        }
        if (null === $idspecialty) {
            return $this->templateJson(404, "Parameter id_specialty is needed", 1, "");
        }
        if ($idPatient == null) {
            return $this->templateJson(404, "Parameter id_patient is needed", 1, "");

        }
        if ($file != null) {
            $tipos = array("jpeg", "jpg", "png", "tiff", "bmp", "raw", "nef");
            foreach ($file as &$valor) {
                if (!in_array($valor->guessExtension(), $tipos)) {
                    return $this->templateJson(404, "El archivo tiene que ser de tipo imagen", 1, "")->setStatusCode(404);
                }
            }
        }
        $econsult = new Econsulta();
        $econsult->setDescripcion($description);
        $econsult->setFecha(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $econsult->setIdPaciente($idPatient);
        $econsult->setIdEspecialidad($idspecialty);
        $econsult->setIdMedico($this->get('security.token_storage')->getToken()->getUser()->getId());

        $entityManager->persist($econsult);
        $entityManager->flush();


        if ($file != null) {
            $id = $econsult->getId();

            foreach ($file as &$valor) {
                $this->crearAdjuntoEConsulta($valor, $id);

            }
            return $this->templateJson(201, "Consult  with attachments created", 1, "")->setStatusCode(201);
        }
        return $this->templateJson(201, "Consult specialty created", 1, "")->setStatusCode(201);
    }

    private function crearAdjuntoEConsulta(File $file, int $id)
    {
        $adjunto = new Adjunto();
        $adjuntoeconsulta = new AdjuntoEconsulta();

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('adjuntos_directory'), $fileName);
        $adjunto->setPath($fileName);
        $adjunto->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        $adjunto->setTamanio($file->getClientSize());
        $adjunto->setNombre("Archivo econsulta");
        $em = $this->getDoctrine()->getManager();
        $em->persist($adjunto);
        $em->flush();
        $adjuntoeconsulta->setIdAdjunto($adjunto->getId());
        $adjuntoeconsulta->setIdEconsulta($id);
        $em->persist($adjuntoeconsulta);
        $em->flush();


    }


    /**
     * @Route("/api/medic/econsultspecialty")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEConsultsSpecialty(Request $request)
    {

        $repository = $this->getDoctrine()->getRepository(Medico::class);

        /** @var $medic Medico */
        $medic = $repository->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $offset = (int)$request->get("offset");
        $conn = $this->getDoctrine()->getConnection();

        $sqlcount = 'SELECT COUNT(DISTINCT e.id) as c
                from econsultas e 
                    left join usuarios u on u.id=e.id_medico
                    LEFT join respuestas_medico_econsulta rme on rme.id_econsulta=e.id
                    left join usuarios u2 on u2.id=rme.id_medico
                    left join medicos m on m.id_usuario=rme.id_medico
                    left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id 
                               from respuestas
                               as r GROUP by r.id  order by fecha desc)  as r
                    on rme.id_respuesta=r.id
                    where e.id_especialidad=:p1
            ';
        $count = $conn->prepare($sqlcount);
        $count->execute(['p1' => $medic->getEspecialidad()]);
        $number = (int)$count->fetch()['c'];
        if ($number == 0) {
            return $this->templateJson(200, "", 1, array("count" => $number, "rows" => []));
        }

        $sql = 'SELECT e.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_respuesta,r.fecha fecharespuesta,r.respuesta,r.leido  from econsultas e 
                    left join usuarios u on u.id=e.id_medico
                    LEFT join respuestas_medico_econsulta rme on rme.id_econsulta=e.id
                    left join usuarios u2 on u2.id=rme.id_medico
                    left join medicos m on m.id_usuario=rme.id_medico
                    left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id 
                               from respuestas
                               as r GROUP by r.id  order by fecha desc)  as r
                    on rme.id_respuesta=r.id
                    where e.id_especialidad=:p1
                    GROUP by e.id
                    ORDER BY greatest (e.fecha  ,ifnull( r.fecha,0)) desc limit 20 offset ' . $offset;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $medic->getEspecialidad()]);
        return $this->templateJson(200, "", 1, array("count" => $number, "rows" => $stmt->fetchAll()));


    }

    /**
     * @Route("/api/medic/econsults")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEConsults(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Medico::class);

        /** @var $medic Medico */
        $medic = $repository->findOneBy(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        $offset = (int)$request->get("offset");
        $conn = $this->getDoctrine()->getConnection();

        $sqlcount = 'SELECT COUNT(DISTINCT e.id) as c
                from econsultas e 
                    left join usuarios u on u.id=e.id_medico
            LEFT join medicos m on m.id_usuario=e.id_medico
            LEFT join respuestas_medico_econsulta rme on rme.id_econsulta=e.id
             LEFT join usuarios u2 on u2.id=rme.id_medico
            left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id from respuestas
                       as r GROUP by r.id  order by fecha desc)  as r
            on rme.id_respuesta=r.id
            where m.id_usuario=:p1
            ';
        $count = $conn->prepare($sqlcount);
        $count->execute(['p1' => $medic->getId()]);
        $number = (int)$count->fetch()['c'];
        if ($number == 0) {
            return $this->templateJson(200, "", 1, array("count" => $number, "rows" => []));
        }

        $sql = 'SELECT e.*,Concat(u.nombre,\' \',u.apellido) nombre,Concat(u2.nombre,\' \',u2.apellido) nombre_respuesta,r.fecha fecharespuesta,r.respuesta,r.leido  from econsultas e 
                    left join usuarios u on u.id=e.id_medico
            LEFT join medicos m on m.id_usuario=e.id_medico
            LEFT join respuestas_medico_econsulta rme on rme.id_econsulta=e.id
             LEFT join usuarios u2 on u2.id=rme.id_medico
            left join (SELECT MAX(r.fecha) fecha , r.respuesta,r.leido,r.id from respuestas
                       as r GROUP by r.id  order by fecha desc)  as r
            on rme.id_respuesta=r.id
            where m.id_usuario=:p1
            GROUP by e.id
            ORDER BY greatest (e.fecha  ,ifnull( r.fecha,0)) desc limit 20 offset ' . $offset;
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $medic->getId()]);
        return $this->templateJson(200, "", 1, array("count" => $number, "rows" => $stmt->fetchAll()));
    }

    /**
     * @Route("/api/medic/econsults/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getConsult($id)
    {
        $conn = $this->getDoctrine()->getConnection();
        $sql3 = "SELECT e.*,Concat(u.nombre,' ',u.apellido) paciente,Concat(u2.nombre,' ',u2.apellido) medico from econsultas e inner join usuarios u on u.id = e.id_paciente inner join usuarios u2 on u2.id = e.id_medico where e.id=:id";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->execute(['id' => $id]);

        $sql = "SELECT a.* from adjuntos a inner join adjuntos_econsultas ae on ae.id_adjunto=a.id inner join econsultas e on e.id=ae.id_econsulta where ae.id_econsulta=:p1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $id]);
        $sql2 = "SELECT r.*,Concat(u.nombre,' ',u.apellido) nombre from respuestas r
            left join respuestas_medico_econsulta rme on r.id=rme.id_respuesta 
            left join usuarios u on u.id=rme.id_medico
            left join econsultas e on e.id=rme.id_econsulta
            where rme.id_econsulta=:p1
            order by r.fecha desc ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['p1' => $id]);

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => array("EConsult" => $stmt3->fetch(), "Attachments" => $stmt->fetchall(), "Respuestas" => $stmt2->fetchAll()))));
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