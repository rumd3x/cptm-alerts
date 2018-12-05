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
}
