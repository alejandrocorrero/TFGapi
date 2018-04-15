<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Medico
 *
 * @ORM\Table("medicos")
 * @ORM\Entity
 */
class Medico
{

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_usuario")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $id;
    /**
     * @ORM\Column(name="id_especialidad")
     * @ORM\ManyToOne(targetEntity="Especialidad")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $especialidad;

    /**
     * @ORM\Column(name="id_centro")
     * @ORM\ManyToOne(targetEntity="Centro")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $centro;

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
    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    /**
     * @param mixed $especialidad
     */
    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;
    }

    /**
     * @return mixed
     */
    public function getCentro()
    {
        return $this->centro;
    }

    /**
     * @param mixed $centro
     */
    public function setCentro($centro)
    {
        $this->centro = $centro;
    }




}