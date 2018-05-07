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
     * @ORM\ManyToOne(targetEntity="Econsulta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idEconsulta;

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
    public function getIdEconsulta()
    {
        return $this->idEconsulta;
    }

    /**
     * @param int $idEconsulta
     */
    public function setIdEconsulta($idEconsulta)
    {
        $this->idEconsulta = $idEconsulta;
    }


}