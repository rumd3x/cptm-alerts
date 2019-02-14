<?php
namespace CptmAlerts\Modules\Notification\Factory;

use Rumd3x\Slack\Field;
use Rumd3x\Slack\Attachment;
use CptmAlerts\Classes\LineStatusDiff;
use Tightenco\Collect\Support\Collection;
use Rumd3x\Slack\Notification as SlackNotification;

class SlackNotificationFactory implements NotificationFactoryInterface
{
    /**
     * @param \Tightenco\Collect\Support\Collection of LineStatusDiff $diffCollection
     * @return SlackNotification
     */
    public function make(Collection $diffCollection)
    {
        $notification = new SlackNotification(
            $this->makeHeadline($diffCollection),
            getenv('SLACK_CHANNEL'),
            getenv('SLACK_BOT_USERNAME')
        );

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
     * @param Collection $diffCollection
     * @return string
     */
    private function makeHeadline(Collection $diffCollection)
    {
        if ($diffCollection->count() > 1) {
            return sprintf(
                "Atenção para %d mudanças de status nas linhas %s.",
                $diffCollection->count(),
                implode(', ', $diffCollection->map(function ($diff) {
                    return $diff->getNew()->getLine()->linha;
                })->toArray())
            );
        }

        return sprintf(
            "Alteração na Linha %d: %s",
            $diffCollection->first()->getNew()->getLine()->linha,
            $diffCollection->first()->getNew()->situacao
        );
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
