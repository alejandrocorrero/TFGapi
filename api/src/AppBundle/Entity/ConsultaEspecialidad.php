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
 * @ORM\Table("consultas_especialidades")
 * @ORM\Entity
 */
class ConsultaEspecialidad
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
     * @ORM\Column(name="id_especialidad")
     * @ORM\ManyToOne(targetEntity="Especialidad")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $idEspecialidad;

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
    public function getIdEspecialidad()
    {
        return $this->idEspecialidad;
    }

    /**
     * @param int $idEspecialidad
     */
    public function setIdEspecialidad($idEspecialidad)
    {
        $this->idEspecialidad = $idEspecialidad;
    }



}