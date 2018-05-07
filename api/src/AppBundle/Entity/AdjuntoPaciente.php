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
 * AdjuntoPaciente
 *
 * @ORM\Table("adjuntos_pacientes")
 * @ORM\Entity
 */
class AdjuntoPaciente
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
     * @ORM\Column(name="id_paciente")
     * @ORM\ManyToOne(targetEntity="Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idPaciente;

    /**
     * @return int
     */
    public function getIdAdjunto(): int
    {
        return $this->idAdjunto;
    }

    /**
     * @param int $idAdjunto
     */
    public function setIdAdjunto(int $idAdjunto)
    {
        $this->idAdjunto = $idAdjunto;
    }

    /**
     * @return int
     */
    public function getIdPaciente(): int
    {
        return $this->idPaciente;
    }

    /**
     * @param int $idPaciente
     */
    public function setIdPaciente(int $idPaciente)
    {
        $this->idPaciente = $idPaciente;
    }




}