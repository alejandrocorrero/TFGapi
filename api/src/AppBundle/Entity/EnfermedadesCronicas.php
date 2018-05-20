<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 18:06
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * EnfermedadesCronicas
 *
 * @ORM\Table("enfermedades_cronicas")
 * @ORM\Entity
 */
class EnfermedadesCronicas
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
     * @ORM\Column(type="string")
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $gravedad;
    /**
     * @ORM\Column(type="date", name="fecha_deteccion")
     */
    protected $fechaDeteccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_paciente")
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idPaciente;

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
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getGravedad()
    {
        return $this->gravedad;
    }

    /**
     * @param mixed $gravedad
     */
    public function setGravedad($gravedad)
    {
        $this->gravedad = $gravedad;
    }

    /**
     * @return mixed
     */
    public function getFechaDeteccion()
    {
        return $this->fechaDeteccion;
    }

    /**
     * @param mixed $fechaDeteccion
     */
    public function setFechaDeteccion($fechaDeteccion)
    {
        $this->fechaDeteccion = $fechaDeteccion;
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





}