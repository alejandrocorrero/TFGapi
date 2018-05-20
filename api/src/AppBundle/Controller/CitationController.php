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


class CitationController extends FOSRestController
{
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