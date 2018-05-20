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


class EConsultController extends FOSRestController
{ /**
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