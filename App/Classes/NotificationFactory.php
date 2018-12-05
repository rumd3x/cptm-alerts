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
        $attachment->addField('Foi de operacao normal para com lentidao', '', false);
        $attachment->addField('Desde ', '12:34', true);
        $attachment->addField('Última atualização ', '15:22', true);
        $notification->attach($attachment);

        $attachment = new Attachment('Mudança na linha 4');
        $attachment->withFooter()->setColor(Attachment::COLOR_SUCCESS);
        $attachment->addField('Melhoro sabosta', false);
        $attachment->addField('Desde ', '12:34', true);
        $attachment->addField('Última atualização ', '15:22', true);
        $notification->attach($attachment);
        return $notification;
    }
}
