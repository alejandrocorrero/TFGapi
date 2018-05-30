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


class ChronicController extends FOSRestController
{
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
     * @Route("/api/medic/user/{user}/chronic")
     */
    public function getChronicMedic($user)
    {
        $id = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $chronic = $entityManager->getRepository(EnfermedadesCronicas::class)->findBy(['idPaciente' => $user]);

        if (!$chronic) {
            return $this->handleView($this->view(array("status" => 404, "message" => "No existe", "type" => 1, "data" => [])));

        }

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $chronic)));

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