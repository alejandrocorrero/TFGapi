<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 2:58
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Sexo
 *
 * @ORM\Table("sexos")
 * @ORM\Entity
 */
class Sexo
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
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $nombre;

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



}