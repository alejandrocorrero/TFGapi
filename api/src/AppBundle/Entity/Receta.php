<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 3:25
 */

namespace AppBundle\Entity;

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
     * @ORM\OneToOne(targetEntity="Historial")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idHistorial;
}