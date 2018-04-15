<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 14:13
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * AdjuntoEconsultas
 *
 * @ORM\Table("adjuntos_econsultas")
 * @ORM\Entity
 */
class AdjuntoEconsulta
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_adjunto")
     * @ORM\OneToOne(targetEntity="Adjunto")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idadjunto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_econsulta")
     * @ORM\ManyToOne(targetEntity="Econsultas")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idEconsultas;

    /**
     * @return int
     */
    public function getIdadjunto()
    {
        return $this->idadjunto;
    }

    /**
     * @param int $idadjunto
     */
    public function setIdadjunto($idadjunto)
    {
        $this->idadjunto = $idadjunto;
    }

    /**
     * @return int
     */
    public function getIdEconsultas()
    {
        return $this->idEconsultas;
    }

    /**
     * @param int $idEconsultas
     */
    public function setIdEconsultas($idEconsultas)
    {
        $this->idEconsultas = $idEconsultas;
    }


}