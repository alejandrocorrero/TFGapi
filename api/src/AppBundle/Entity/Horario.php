<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 1:26
 */

namespace AppBundle\Entity;
/**
 * Medico
 *
 * @ORM\Table("horarios")
 * @ORM\Entity
 */
class Horario
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
     * @ORM\Column(name="id_medico")
     * @ORM\ManyToOne(targetEntity="Medico")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $idMedico;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'))
     */
    protected $dia;
    /**
     * @ORM\Column(type="time", name="hora_inicio")
     */
    protected $horaInicio;
    /**
     * @ORM\Column(type="time", name="hora_fin")
     */
    protected $horaFin;
}
