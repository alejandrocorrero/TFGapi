<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 22/04/2018
 * Time: 22:24
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * TipoAjduntio
 *
 * @ORM\Table("tipos_adjunto")
 * @ORM\Entity
 */
class TipoAdjunto
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
    /**
     * @ORM\Column(type="string", length=7)
     */
    protected $color;
    /**
     * @ORM\Column(name="id_adjunto")
     * @ORM\ManyToOne(targetEntity="Adjunto")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $adjunto;

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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getAdjunto()
    {
        return $this->adjunto;
    }

    /**
     * @param mixed $adjunto
     */
    public function setAdjunto($adjunto)
    {
        $this->adjunto = $adjunto;
    }

}