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
     *
     * @ORM\Column(name="id_usuario")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $id;
    /**
     * @ORM\Column(name="id_especialidad")
     * @ORM\ManyToOne(targetEntity="Especialidades")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $especialidad;

    /**
     * @ORM\Column(name="id_especialidad")
     * @ORM\ManyToOne(targetEntity="Centro")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $centro;


}