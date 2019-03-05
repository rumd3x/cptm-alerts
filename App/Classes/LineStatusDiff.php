<?php
namespace CptmAlerts\Classes;

class LineStatusDiff
{
    /**
     * @var LineStatus
     */
    private $oldStatus;

    /**
     * @var LineStatus
     */
    private $newStatus;

    /**
     * @param LineStatus $old
     * @param LineStatus $new
     */
    public function __construct(LineStatus $old, LineStatus $new)
    {
        $this->oldStatus = $old;
        $this->newStatus = $new;
    }

    /**
     * @return LineStatus
     */
    public function getOld()
    {
        return $this->oldStatus;
    }

    /**
     * @return LineStatus
     */
    public function getNew()
    {
        return $this->newStatus;
    }

    /**
     * Returns the level of the warning
     *
     * @return int
     */
    public function getLevel()
    {
        if ($this->isPositive()) {
            return 1;
        }

        if ($this->isReallyBad()) {
            return 3;
        }

        if ($this->isNeutral()) {
            return 0;
        }

        if ($this->isNegative()) {
            return 2;
        }

        return 4;
    }

    /**
     * Discover if the status change was just normal and expected
     *
     * @return boolean
     */
    public function isNeutral()
    {
        $startedNow = strpos(strtolower($this->getOld()->situacao), 'encerrada') !== false;
        $finishedNow = strpos(strtolower($this->getNew()->situacao), 'encerrada') !== false;
        return ($startedNow || $finishedNow);
    }

    /**
     * Discover if the status change was a good thing
     *
     * @return boolean
     */
    public function isPositive()
    {
        $isNormalized = strpos(strtolower($this->getNew()->situacao), 'normal') !== false;
        return ($isNormalized && !$this->isNeutral());
    }

    /**
     * Discover if the status change was a bad thing
     *
     * @return boolean
     */
    public function isNegative()
    {
        return !$this->isPositive();
    }

    /**
     * Discover if the status change is worse than normal
     *
     * @return boolean
     */
    public function isReallyBad()
    {
        $isParalized = strpos(strtolower($this->getNew()->situacao), 'paralisada') !== false;
        $isWorkingPartially = strpos(strtolower($this->getNew()->situacao), 'parcial') !== false;
        return ($isParalized || $isWorkingPartially);
    }
}
