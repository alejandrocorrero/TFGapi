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
use AppBundle\Entity\RespuestaMedicoConsulta;
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


class ResponseController extends FOSRestController
{
    /**
     * @Route("/api/patient/response")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postResponsePaciente(Request $request){
        $responseMsg = $request->get("response");
        $idConsult = $request->get("id_consult");
        if (null === $responseMsg) {
            return $this->templateJson(404, "Parameter response is needed", 1, "");
        }
        if (null === $idConsult) {
            return $this->templateJson(404, "Parameter id_consult is needed", 1, "");
        }
        $consult = $this->getDoctrine()->getRepository(Consulta::class)->findOneBy(["id"=>$idConsult,"idPaciente"=>$this->get('security.token_storage')->getToken()->getUser()->getId()]);
        if($consult==null){
            return $this->templateJson(404, "Not permission", 1, "");

        }
        $entityManager = $this->getDoctrine()->getManager();
        $response = new Respuesta();
        $response->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        $response->setLeido(0);
        $response->setRespuesta($responseMsg);
        $entityManager->persist($response);
        $entityManager->flush();
        $respuestaPaciente= new RespuestaPacienteConsulta();
        $respuestaPaciente->setIdRespuesta($response->getId());
        $respuestaPaciente->setIdConsulta($idConsult);
        $respuestaPaciente->setIdPaciente($this->get('security.token_storage')->getToken()->getUser()->getId());
        $entityManager->persist($respuestaPaciente);
        $entityManager->flush();
        $conn = $this->getDoctrine()->getConnection();
        $sql='Select r.* from respuestas r where r.id=:p1';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $response->getId()]);

        return $this->templateJson(201, "Created", 1, $stmt->fetch())->setStatusCode(201);

    }

    /**
     * @Route("/api/medic/responseconsult")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postResponseMedic(Request $request){
        $responseMsg = $request->get("response");
        $idConsult = $request->get("id_consult");
        if (null === $responseMsg) {
            return $this->templateJson(404, "Parameter response is needed", 1, "");
        }
        if (null === $idConsult) {
            return $this->templateJson(404, "Parameter id_consult is needed", 1, "");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $response = new Respuesta();
        $response->setFecha((DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"))));
        $response->setLeido(0);
        $response->setRespuesta($responseMsg);
        $entityManager->persist($response);
        $entityManager->flush();
        $respuestaMedico= new RespuestaMedicoConsulta();
        $respuestaMedico->setIdRespuesta($response->getId());
        $respuestaMedico->setIdConsulta($idConsult);
        $respuestaMedico->setIdMedico($this->get('security.token_storage')->getToken()->getUser()->getId());
        $entityManager->persist($respuestaMedico);
        $entityManager->flush();
        $conn = $this->getDoctrine()->getConnection();
        $sql='Select r.* from respuestas r where r.id=:p1';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['p1' => $response->getId()]);

        return $this->templateJson(201, "Created", 1, $stmt->fetch())->setStatusCode(201);

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