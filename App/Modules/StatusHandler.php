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
            $statusObj = new LineStatus(new Line($status->codigo));
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
     * @param array $oldStatus
     * @param array $newStatus
     * @return \Tightenco\Collect\Support\Collection of LineStatusDiff
     */
    public function getDiff(array $oldStatus, array $newStatus)
    {
        $return = collect();
        $oldStatus = $this->buildStatus($oldStatus);
        $newStatus = $this->buildStatus($newStatus);

        foreach ($newStatus as $key => $currentNewStatus) {
            $currentOldStatus = $oldStatus->get($key);
            if (!$currentNewStatus->equals($currentOldStatus)) {
                $return->push(new LineStatusDiff($currentOldStatus, $currentNewStatus));
            }
        }

        return $return;
    }
}
