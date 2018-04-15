<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 2:44
 */

namespace AppBundle\Entity;
/**
 * Especialidad
 *
 * @ORM\Table("especialidades")
 * @ORM\Entity
 */

class Especialidad
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
    protected $nombre;
}