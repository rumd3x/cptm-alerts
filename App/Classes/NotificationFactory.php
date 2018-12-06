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

            $attachment->withFooter()->setColor($this->getDiffColor($diff));
            $notification->attach($attachment);
        }
        return $notification;
    }

    /**
     * @param LineStatusDiff $diff
     * @return string
     */
    private function getDiffColor(LineStatusDiff $diff)
    {
        if ($this->isNeutral($diff)) {
            return Attachment::COLOR_DEFAULT;
        }

        if ($this->isPositive($diff)) {
            return Attachment::COLOR_SUCCESS;
        }

        if ($this->isNegative($diff) && $this->isReallyBad($diff)) {
            return Attachment::COLOR_DANGER;
        }

        if ($this->isNegative($diff)) {
            return Attachment::COLOR_WARNING;
        }

        return Attachment::COLOR_INFO;
    }

    /**
     * Discover if the status change was just normal and expected
     *
     * @param LineStatusDiff $diff
     * @return boolean
     */
    private function isNeutral(LineStatusDiff $diff)
    {
        $startedNow = strpos(strtolower($diff->getOld()->situacao), 'encerrada') !== false;
        $finishedNow = strpos(strtolower($diff->getNew()->situacao), 'encerrada') !== false;
        return ($startedNow || $finishedNow);
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
        $isNormalized = strpos(strtolower($diff->getNew()->situacao), 'normal') !== false;
        return (!$isNeutral && $isNormalized);
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

    /**
     * Discover if the status change is really that bad
     *
     * @param LineStatusDiff $diff
     * @return boolean
     */
    private function isReallyBad(LineStatusDiff $diff)
    {
        $isNegative = $this->isNegative($diff);
        $isParalized = strpos(strtolower($diff->getNew()->situacao), 'paralisada') !== false;
        return ($isNegative && $isParalized);
    }
}
