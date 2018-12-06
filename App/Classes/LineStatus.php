<?php
namespace CptmAlerts\Classes;

class LineStatus
{
    /**
     * @var int
     */
    public $linha;

    /**
     * @var \Carbon\Carbon
     */
    public $dthOcorrencia;

    /**
     * @var \Carbon\Carbon
     */
    public $dthAtualizado;

    /**
     * @var string
     */
    public $situacao;

    /**
     * Compares two line status
     *
     * @param self $lineStatus
     * @return bool
     */
    public function equals(LineStatus $otherLineStatus)
    {
        return $this->situacao === $otherLineStatus->situacao;
    }
}
