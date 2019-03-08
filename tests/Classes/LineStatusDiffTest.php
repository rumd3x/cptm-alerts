<?php

use CptmAlerts\Classes\Line;
use PHPUnit\Framework\TestCase;
use CptmAlerts\Classes\LineStatus;
use Carbon\Carbon;
use CptmAlerts\Classes\LineStatusDiff;

final class LineStatusDiffTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromVelocidadeReduzidaToOperacaoNormal()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Velocidade Reduzida";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Normal";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isPositive());
        $this->assertFalse($diff->isNegative());
        $this->assertFalse($diff->isNeutral());
        $this->assertFalse($diff->isReallyBad());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 1);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoParalizadaToOperacaoNormal()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Paralisada";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Normal";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isPositive());
        $this->assertFalse($diff->isNegative());
        $this->assertFalse($diff->isNeutral());
        $this->assertFalse($diff->isReallyBad());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 1);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoNormalToOperacaoParalisada()
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
        $status2->situacao = "Operação Paralisada";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertTrue($diff->isReallyBad());
        $this->assertFalse($diff->isPositive());
        $this->assertFalse($diff->isNeutral());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 3);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoNormalToOperacaoParcial()
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
        $status2->situacao = "Operação Parcial";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertTrue($diff->isReallyBad());
        $this->assertFalse($diff->isPositive());
        $this->assertFalse($diff->isNeutral());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 3);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoNormalToVelocidadeReduzida()
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
        $status2->situacao = "Velocidade Reduzida";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertFalse($diff->isReallyBad());
        $this->assertFalse($diff->isPositive());
        $this->assertFalse($diff->isNeutral());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 2);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoNormalToOperacaoEncerrada()
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
        $status2->situacao = "Operação Encerrada";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNeutral());
        $this->assertFalse($diff->isNegative());
        $this->assertFalse($diff->isReallyBad());
        $this->assertFalse($diff->isPositive());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 0);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromVelocidadeReduzidaToOperacaoEncerrada()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Velocidade Reduzida";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Encerrada";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNeutral());
        $this->assertFalse($diff->isNegative());
        $this->assertFalse($diff->isReallyBad());
        $this->assertFalse($diff->isPositive());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 0);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoEncerradaToOperacaoNormal()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Encerrada";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Normal";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNeutral());
        $this->assertFalse($diff->isPositive());
        $this->assertFalse($diff->isNegative());
        $this->assertFalse($diff->isReallyBad());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 0);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoEncerradaToVelocidadeReduzida()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Encerrada";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Velocidade Reduzida";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertFalse($diff->isNeutral());
        $this->assertFalse($diff->isPositive());
        $this->assertFalse($diff->isReallyBad());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 2);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoEncerradaToOperacaoParcial()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Encerrada";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Parcial";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertTrue($diff->isReallyBad());
        $this->assertFalse($diff->isNeutral());
        $this->assertFalse($diff->isPositive());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 3);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return void
     */
    public function testFromOperacaoEncerradaToOperacaoParalizada()
    {
        $line = new Line(1);

        $status1 = new LineStatus($line);
        $status1->dthOcorrencia = Carbon::now();
        $status1->dthAtualizado = Carbon::now();
        $status1->situacao = "Operação Encerrada";
        $status1->descricao = "";

        $status2 = new LineStatus($line);
        $status2->dthOcorrencia = Carbon::now()->addMinute();
        $status2->dthAtualizado = Carbon::now()->addMinute();
        $status2->situacao = "Operação Paralizada";
        $status2->descricao = "";

        $diff = new LineStatusDiff($status1, $status2);

        $this->assertTrue($diff->isNegative());
        $this->assertTrue($diff->isReallyBad());
        $this->assertFalse($diff->isNeutral());
        $this->assertFalse($diff->isPositive());
        $this->assertIsInt($diff->getLevel());
        $this->assertEquals($diff->getLevel(), 3);
    }
}
