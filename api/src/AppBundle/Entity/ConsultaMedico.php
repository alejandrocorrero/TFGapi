<?php
/**
 * Created by PhpStorm.
 * User: er_al
 * Date: 15/04/2018
 * Time: 17:02
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * consultas
 *
 * @ORM\Table("consultas_medicos")
 * @ORM\Entity
 */
class ConsultaMedico
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id_consulta")
     * @ORM\OneToOne(targetEntity="Consulta")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idConsulta;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_medico")
     * @ORM\ManyToOne(targetEntity="Medico")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idMedico;

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

    /**
     * @return int
     */
    public function getIdMedico()
    {
        return $this->idMedico;
    }

    /**
     * @param int $idMedico
     */
    public function setIdMedico($idMedico)
    {
        $this->idMedico = $idMedico;
    }


}