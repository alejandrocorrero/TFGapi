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


class UserController extends FOSRestController
{
    /**
     * @Route("/api/patient/user")
     */
    public function getUser()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_MEDICO')) {
            $type=2;
        } else {
            $type=1;

        }
        $conn = $this->getDoctrine()->getConnection();
        $sql = 'SELECT u.id,u.nombre,u.apellido,u.email,u.direccion,u.fecha_nacimiento,u.telefono,u.movil,pa.nombre as pais_nacimiento,s.nombre as sexo,ec.nombre as estado_civil,u.ocupacion,u.notas,u.foto,CONCAT(u2.nombre," " ,u2.apellido)as nombre_medico, u2.id as id_medico 
                    FROM usuarios u 
                    inner join pacientes p on u.id=p.id_usuario 
                    inner join usuarios u2 on p.id_medico=u2.id 
                    inner join sexos s on s.id=u.id_sexo 
                    inner join estados_civiles ec on ec.id=u.id_estado_civil
                    inner join paises pa on pa.id=u.id_pais 
                    WHERE u.id=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => $type, "data" => $stmt->fetch())));


    }

    /**
     * @Route("/api/medic/user/{id}")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserMedic($id)
    {


        $conn = $this->getDoctrine()->getConnection();
        $sql = 'SELECT u.id,u.nombre,u.apellido,u.email,u.direccion,u.fecha_nacimiento,u.telefono,u.movil,pa.nombre as pais_nacimiento,s.nombre as sexo,ec.nombre as estado_civil,u.ocupacion,u.notas,u.foto,CONCAT(u2.nombre," " ,u2.apellido)as nombre_medico, u2.id as id_medico 
                    FROM usuarios u 
                    inner join pacientes p on u.id=p.id_usuario 
                    inner join usuarios u2 on p.id_medico=u2.id 
                    inner join sexos s on s.id=u.id_sexo 
                    inner join estados_civiles ec on ec.id=u.id_estado_civil
                    inner join paises pa on pa.id=u.id_pais 
                    WHERE u.id=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetch())));


    }

    /**
     * @Route("/api/medic/users")
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsers(Request $request)
    {

        $filter = $request->get("filter");
        $sql = 'SELECT u.id,u.username,u.nombre,u.apellido,u.foto FROM usuarios u  INNER join pacientes p on p.id_usuario=u.id ';
        if ($filter != null) {
            $query = 'where CONCAT(u.nombre,\' \',u.apellido)LIKE \'%' . $filter . '%\'';
            $sql = $sql . $query;
            $conn = $this->getDoctrine()->getConnection();

            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } else {
            $query = 'where p.id_medico=:id';
            $sql = $sql . $query;
            $conn = $this->getDoctrine()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $this->get('security.token_storage')->getToken()->getUser()->getId()]);
        }

        return $this->handleView($this->view(array("status" => 200, "message" => "", "type" => 1, "data" => $stmt->fetchall())));


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