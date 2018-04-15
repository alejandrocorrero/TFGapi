<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 2:58
 */

namespace AppBundle\Entity;

/**
 * Paciente
 *
 * @ORM\Table("pacientes")
 * @ORM\Entity
 */
class Paciente
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
     * @var integer
     *
     * @ORM\Column(name="id_medico")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idMedico;
}