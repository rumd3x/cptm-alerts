<?php
namespace CptmAlerts\Classes;

class LineStatus
{
    const OPERACAO_NORMAL = 'Operação Normal';
    const VELOCIDADE_REDUZIDA = 'Velocidade Reduzida';
    const OPERACAO_ENCERRADA = 'Operações Encerradas';

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
     * @var string
     */
    public $descricao;

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
