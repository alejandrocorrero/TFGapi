<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 13:30
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Receta
 *
 * @ORM\Table("historial")
 * @ORM\Entity
 */
class Historial
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_medico")
     * @ORM\ManyToOne(targetEntity="Medico")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idMedico;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_paciente")
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idPaciente;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $causa;
    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $notas;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $fecha;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return int
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * @param int $idPaciente
     */
    public function setIdPaciente($idPaciente)
    {
        $this->idPaciente = $idPaciente;
    }

    /**
     * @return mixed
     */
    public function getCausa()
    {
        return $this->causa;
    }

    /**
     * @param mixed $causa
     */
    public function setCausa($causa)
    {
        $this->causa = $causa;
    }

    /**
     * @return mixed
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * @param mixed $notas
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }



}