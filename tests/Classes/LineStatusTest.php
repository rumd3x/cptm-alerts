<?php

use CptmAlerts\Classes\Line;
use PHPUnit\Framework\TestCase;
use CptmAlerts\Classes\LineStatus;
use Carbon\Carbon;

final class LineStatusTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testLineStatus()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Normal";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Normal";
        $status2->descricao = "";

        $status3 = new LineStatus($line);
        $status3->dthOcorrencia = Carbon::now();
        $status3->dthAtualizado = Carbon::now();
        $status3->situacao = "Velocidade Reduzida";
        $status3->descricao = "Sem previsão de reestabelecimento.";

        $status4 = new LineStatus($line);
        $status4->dthOcorrencia = Carbon::now();
        $status4->dthAtualizado = Carbon::now();
        $status4->situacao = "Velocidade Reduzida";
        $status4->descricao = "Reestabelecimento em 5 minutos.";

        $this->assertInstanceOf(get_class($status1->getLine()), $line);
        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
        $this->assertFalse($status3->equals($status4));
    }
}
