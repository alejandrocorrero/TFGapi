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
 * @ORM\Table("respuestas_medico_econsulta")
 * @ORM\Entity
 */

class RespuestaMedicoEconsulta
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
     * @ORM\Id
     * @ORM\Column(name="id_econsulta")
     * @ORM\ManyToOne(targetEntity="Econsulta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idEConsulta;
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_medico")
     * @ORM\ManyToOne(targetEntity="Medico")
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
    public function getIdEConsulta()
    {
        return $this->idEConsulta;
    }

    /**
     * @param int $idEConsulta
     */
    public function setIdEConsulta($idEConsulta)
    {
        $this->idEConsulta = $idEConsulta;
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