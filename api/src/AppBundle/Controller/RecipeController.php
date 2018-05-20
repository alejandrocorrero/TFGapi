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


class RecipeController extends FOSRestController
{
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