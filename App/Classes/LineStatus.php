<?php
namespace CptmAlerts\Classes;

class LineStatus
{
    /**
     * @var Line
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
     * @var string
     */
    public $descricao;

    /**
     * @param Line $line
     * @return self
     */
    public function __construct(Line $line)
    {
        $this->line = $line;
        return $this;
    }

    /**
     * @return Line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Compares two line status
     *
     * @param self $lineStatus
     * @return bool
     */
    public function equals(LineStatus $otherLineStatus)
    {
        $situacaoEquals = $this->situacao === $otherLineStatus->situacao;
        $descricaoEquals = $this->descricao === $otherLineStatus->descricao;
        return ($situacaoEquals && $descricaoEquals);
    }
}
