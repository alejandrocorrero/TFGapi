<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 16:37
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Econsultas
 *
 * @ORM\Table("econsultas")
 * @ORM\Entity
 */
class Econsulta
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
     * @ORM\Column(name="id_paciente")
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idPaciente;
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
     * @ORM\Column(name="id_especialidad")
     * @ORM\ManyToOne(targetEntity="Especialidad")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idEspecialidad;
    /**
     * @ORM\Column(type="datetime", name="fecha")
     */
    protected $fecha;
    /**
     * @ORM\Column(type="string", length=10000)
     */
    protected $descripcion;

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
    public function getIdEspecialidad()
    {
        return $this->idEspecialidad;
    }

    /**
     * @param int $idEspecialidad
     */
    public function setIdEspecialidad($idEspecialidad)
    {
        $this->idEspecialidad = $idEspecialidad;
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

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }


}