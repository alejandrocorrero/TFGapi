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
 * AdjuntoConsultas
 *
 * @ORM\Table("adjuntos_consultas")
 * @ORM\Entity
 */
class AdjuntoConsulta
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_adjunto")
     * @ORM\OneToOne(targetEntity="Adjunto")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idAdjunto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_Consulta")
     * @ORM\ManyToOne(targetEntity="Consulta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idConsulta;

    /**
     * @return int
     */
    public function getIdAdjunto()
    {
        return $this->idAdjunto;
    }

    /**
     * @param int $idAdjunto
     */
    public function setIdAdjunto($idAdjunto)
    {
        $this->idAdjunto = $idAdjunto;
    }

    /**
     * @return int
     */
    public function getIdConsulta()
    {
        return $this->idConsulta;
    }

    /**
     * @param int $idConsulta
     */
    public function setIdConsulta($idConsulta)
    {
        $this->idConsulta = $idConsulta;
    }



}