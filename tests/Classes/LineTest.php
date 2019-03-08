<?php

use CptmAlerts\Classes\Line;
use PHPUnit\Framework\TestCase;

final class LineTest extends TestCase
{
    public function testLine()
    {
        $line = new Line(1);
        $this->assertInstanceOf(Line::class, $line);
        $this->assertEquals($line->linha, 1);
        $this->assertEquals($line->nome, "Azul");
    }
}
