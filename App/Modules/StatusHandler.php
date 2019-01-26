<?php
namespace CptmAlerts\Modules;

use Carbon\Carbon;
use CptmAlerts\Classes\Line;
use CptmAlerts\Classes\LineStatus;
use CptmAlerts\Classes\LineStatusDiff;
use GuzzleHttp\Client as Guzzle;
use Rumd3x\Persistence\Engine;

class StatusHandler
{
    /**
     * @var \Rumd3x\Persistence\Engine
     */
    private $persistence;

    /**
     * @var StdClass[]
     */
    private $currentStatus;

    /**
     * @var StdClass[]
     */
    private $previousStatus;

    public function __construct()
    {
        $this->persistence = new Engine(__DIR__.'/../../Storage/Persistence/cptm');
        $this->currentStatus = $this->retrieveCurrentStatus();
        $this->previousStatus = $this->retrievePreviousStatus();
    }

    /**
     * @return StdClass[]
     */
    public function getPrevious()
    {
        return $this->previousStatus;
    }

    /**
     * @return StdClass[]
     */
    public function getCurrent()
    {
        return $this->currentStatus;
    }

    /**
     * @return StdClass[]
     */
    private function retrievePreviousStatus()
    {
        return $this->persistence->retrieve();
    }

    /**
     * @return StdClass[]
     */
    private function retrieveCurrentStatus()
    {
        $guzzle = new Guzzle();
        $response = $guzzle->get('https://www.diretodostrens.com.br/api/status');
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return self
     */
    public function persistCurrentStatus()
    {
        $this->persistence->store($this->currentStatus);
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param array $statusArray
     * @return \Tightenco\Collect\Support\Collection of LineStatus
     */
    private function buildStatus(array $statusArray)
    {
        $return = collect();
        foreach ($statusArray as $status) {
            $line = new Line($status->codigo);
            if (!$this->shouldNotifyLine($line)) {
                continue;
            }
            $statusObj = new LineStatus($line);
            $statusObj->dthOcorrencia = Carbon::parse($status->criado);
            $statusObj->dthAtualizado = Carbon::parse($status->modificado);
            $statusObj->situacao = $status->situacao;
            if (isset($status->descricao)) {
                $statusObj->descricao = $status->descricao;
            }
            $return->push($statusObj);
        }
        return $return;
    }

    /**
     * Parse NOTIFY_LINES config and filters by it
     *
     * @param Line $line
     * @return bool
     */
    private function shouldNotifyLine(Line $line)
    {
        $linesToNotify = getenv('NOTIFY_LINES');

        if (strtolower(trim($linesToNotify)) === 'all') {
            return true;
        }

        $linesToNotify = collect(explode(',', $linesToNotify))->map(function ($item) {
            return (int) $item;
        });

        return $linesToNotify->contains($line->linha);
    }

    /**
     * @param array $oldStatus
     * @param array $newStatus
     * @return \Tightenco\Collect\Support\Collection of LineStatusDiff
     */
    public function getAllDiff(array $oldStatus, array $newStatus)
    {
        $allDiffs = collect();
        $oldStatus = $this->buildStatus($oldStatus);
        $newStatus = $this->buildStatus($newStatus);

        foreach ($newStatus as $key => $currentNewStatus) {
            $currentOldStatus = $oldStatus->get($key);
            if ($currentNewStatus->equals($currentOldStatus)) {
                continue;
            }
            $diff = new LineStatusDiff($currentOldStatus, $currentNewStatus);
            if ($diff->getLevel() < getenv('NOTIFY_LEVEL')) {
                continue;
            }
            $allDiffs->push($diff);
        }

        return $allDiffs;
    }
}
