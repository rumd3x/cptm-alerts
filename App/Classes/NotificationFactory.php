<?php
namespace CptmAlerts\Classes;

use Tightenco\Collect\Support\Collection;

class NotificationFactory
{
    /**
     * @param \Tightenco\Collect\Support\Collection of LineStatusDiff $diff
     * @return Notification
     */
    public function make(Collection $diff)
    {
        $notification = new Notification('Atenção para 2 mudanças:');

        $attachment = new Attachment('Mudança na linha 11');
        $attachment->withFooter()->setColor(Attachment::COLOR_DANGER);
        $attachment->addField(new Field('big', 'Foi de operacao normal para com lentidao'));
        $attachment->addField(new Field('short', 'Desde ', '12:34'));
        $attachment->addField(new Field('short', 'Última atualização ', '15:22'));
        $notification->attach($attachment);

        $attachment = new Attachment('Mudança na linha 4');
        $attachment->withFooter()->setColor(Attachment::COLOR_SUCCESS);
        $attachment->addField(new Field('big', 'Melhoro sabosta'));
        $attachment->addField(new Field('short', 'Desde ', '12:34'));
        $attachment->addField(new Field('short', 'Última atualização ', '15:22'));
        $notification->attach($attachment);
        return $notification;
    }

    /**
     * Discover if the status change was just normal and expected
     *
     * @param LineStatusDiff $diff
     * @return boolean
     */
    private function isNeutral(LineStatusDiff $diff)
    {
        return $diff->getOld()->situacao !== LineStatus::OPERACAO_ENCERRADA;
    }

    /**
     * Discover if the status change was a good thing
     *
     * @param LineStatusDiff $diff
     * @return boolean
     */
    private function isPositive(LineStatusDiff $diff)
    {
        $isNeutral = $this->isNeutral($diff);
        $isPositive = $diff->getNew()->situacao === LineStatus::OPERACAO_NORMAL;
        return (!$isNeutral && $isPositive);
    }

    /**
     * Discover if the status change was a bad thing
     *
     * @param LineStatusDiff $diff
     * @return boolean
     */
    private function isNegative(LineStatusDiff $diff)
    {
        return !$this->isPositive($diff);
    }
}
