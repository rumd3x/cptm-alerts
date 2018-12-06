<?php
namespace CptmAlerts\Classes;

class Line
{
    /**
     * @var int
     */
    public $linha;

    /**
     * @var string
     */
    public $nome;

    public function __construct(int $linha)
    {
        $this->linha = $linha;
        $this->nome = $this->getLineName();
    }

    /**
     * @return string
     */
    private function getLineName()
    {
        $names = [
            1 => "Azul",
            2 => "Verde",
            3 => "Vermelha",
            4 => "Amarela",
            5 => "Lilás",
            6 => "Laranja",
            7 => "Rubi",
            8 => "Diamante",
            9 => "Esmeralda",
            10 => "Turquesa",
            11 => "Coral",
            12 => "Safira",
            13 => "Jade",
            15 => "Prata",
            17 => "Ouro",
        ];

        return $names[$this->linha];
    }
}
