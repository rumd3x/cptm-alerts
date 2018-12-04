<?php
namespace CptmAlerts\Modules;

use Rumd3x\Persistence\Engine;
use GuzzleHttp\Client as Guzzle;

class LineStatusHandler {
    /**
     * @var \Rumd3x\Persistence\Engine
     */
    private $persistence;
    private $currentStatus;
    private $previousStatus;

    public function __construct() {
        $this->persistence = new Engine('../../../../Storage/Persistence/cptm');
        $this->currentStatus = $this->retrieveCurrentStatus();
        $this->previousStatus = $this->retrievePreviousStatus();
    }

    public function getPrevious() {
        return $this->previousStatus;
    }

    public function getCurrent() {
        return $this->currentStatus;
    }

    private function retrievePreviousStatus() {
        return $this->persistence->retrieve();
    }

    private function retrieveCurrentStatus()
    {
        $guzzle = new Guzzle();
        $response = $guzzle->get('https://www.diretodostrens.com.br/api/status');
        return json_decode($response->getBody()->getContents());
    }    

    public function persistCurrentStatus() {
        $this->persistence->store($this->currentStatus);
        return $this;
    }

    private function reIndexByLineNumber(array $statusArray) {
        $newArray = [];
        foreach ($statusArray as $value) {
            $codigo = (string) $value->codigo;
            unset($value->id);
            unset($value->codigo);
            $newArray[$codigo] = $value;
        }
        return $newArray;
    }

    public function getDiff(array $oldStatus, array $newStatus) {
        $oldStatus = $this->reIndexByLineNumber($oldStatus);
        $newStatus = $this->reIndexByLineNumber($newStatus);
        $diff = [];
        foreach ($newStatus as $linha => $currentNewStatus) {
            $currentOldStatus = $oldStatus[$linha];
            if ($currentNewStatus->situacao !== $currentOldStatus->situacao) {
                $diff[$linha] = [
                    'old' => $oldStatus,
                    'new' => $currentNewStatus,
                ];
            }
        }
        return $diff;
    }
}