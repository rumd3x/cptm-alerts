<?php
namespace CptmAlerts\Classes;

use Tightenco\Collect\Support\Collection;

class NotificationFactory
{
    /**
     * @param \Tightenco\Collect\Support\Collection of LineStatusDiff $diffCollection
     * @return Notification
     */
    public function make(Collection $diffCollection)
    {
        $notification = new Notification(sprintf(
            '*----- Atenção para %d mudança(s) de status nas linhas -----*',
            $diffCollection->count()
        ));

        foreach ($diffCollection as $diff) {
            $headline = sprintf(
                'Mudança na linha %d (%s):',
                $diff->getNew()->getLine()->linha,
                $diff->getNew()->getLine()->nome
            );

            $bigFieldTop = sprintf(
                'Estava em %s e agora está com %s',
                strtolower($diff->getOld()->situacao),
                strtolower($diff->getNew()->situacao)
            );

            $bigFieldBottom = '';
            if (!empty($diff->getNew()->descricao)) {
                $bigFieldBottom = $diff->getNew()->descricao;
            }

            $attachment = new Attachment($headline);
            $attachment->addField(new Field('big', $bigFieldTop, $bigFieldBottom));

            $attachment->withFooter()->setColor($this->getColor($diff));
            $notification->attach($attachment);
        }
        return $notification;
    }

    /**
     * @param LineStatusDiff $diff
     * @return string
     */
    private function getColor(LineStatusDiff $diff)
    {
        $colors = [
            0 => Attachment::COLOR_DEFAULT,
            1 => Attachment::COLOR_SUCCESS,
            2 => Attachment::COLOR_WARNING,
            3 => Attachment::COLOR_DANGER,
        ];

        $level = $diff->getLevel();

        return isset($colors[$level]) ? $colors[$level] : Attachment::COLOR_INFO;
    }
}
