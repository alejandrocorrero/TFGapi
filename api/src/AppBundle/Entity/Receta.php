<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 3:25
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Receta
 *
 * @ORM\Table("recetas")
 * @ORM\Entity
 */
class Receta
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
     * @ORM\Column(type="string", length=100)
     */
    protected $dosis;
    /**
     * @ORM\Column(type="integer")
     */
    protected $duracion;

    /**
     * @ORM\Column(type="integer", name="tiempo_entre_dosis")
     */
    protected $tiempoEntreDosis;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_historial")
     * @ORM\ManyToOne(targetEntity="Historial")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idHistorial;
    /**
     * @ORM\Column(type="date", name="fecha")
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
     * @return mixed
     */
    public function getDosis()
    {
        return $this->dosis;
    }

    /**
     * @param mixed $dosis
     */
    public function setDosis($dosis)
    {
        $this->dosis = $dosis;
    }

    /**
     * @return mixed
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * @param mixed $duracion
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    }

    /**
     * @return mixed
     */
    public function getTiempoEntreDosis()
    {
        return $this->tiempoEntreDosis;
    }

    /**
     * @param mixed $tiempoEntreDosis
     */
    public function setTiempoEntreDosis($tiempoEntreDosis)
    {
        $this->tiempoEntreDosis = $tiempoEntreDosis;
    }

    /**
     * @return int
     */
    public function getIdHistorial()
    {
        return $this->idHistorial;
    }

    /**
     * @param int $idHistorial
     */
    public function setIdHistorial($idHistorial)
    {
        $this->idHistorial = $idHistorial;
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