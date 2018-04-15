<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 19:20
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Respuestas
 *
 * @ORM\Table("respuestas_paciente_consulta")
 * @ORM\Entity
 */

class RespuestaPacienteConsulta
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_respuesta")
     * @ORM\OneToOne(targetEntity="Respuesta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idRespuesta;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_consulta")
     * @ORM\ManyToOne(targetEntity="Consulta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idConsulta;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_paciente")
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idMedico;

    /**
     * @return int
     */
    public function getIdRespuesta()
    {
        return $this->idRespuesta;
    }

    /**
     * @param int $idRespuesta
     */
    public function setIdRespuesta($idRespuesta)
    {
        $this->idRespuesta = $idRespuesta;
    }

    /**
     * @return int
     */
    public function getIdConsulta()
    {
        return $this->idConsulta;
    }

    /**
     * @param int $idConsulta
     */
    public function setIdConsulta($idConsulta)
    {
        $this->idConsulta = $idConsulta;
    }

    /**
     * @return int
     */
    public function getIdMedico()
    {
        return $this->idMedico;
    }

    /**
     * @param int $idMedico
     */
    public function setIdMedico($idMedico)
    {
        $this->idMedico = $idMedico;
    }



}