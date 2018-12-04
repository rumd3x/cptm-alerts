<?php
namespace CptmAlerts\Modules;

use Exception;
use Monolog\Logger;
use Rumd3x\Persistence\Engine;

class Core
{
    /**
     * @var \Monolog\Logger
     */
    private $logger; 

    /**
     * @var LineStatusHandler
     */
    private $lineHandler;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param Logger $logger
     * @param string $slackKey
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->notifier = new Notifier();
        $this->lineHandler = new LineStatusHandler();
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->logger->info("Checkpoint: Application is running");
        
        $oldStatus = $this->lineHandler->getPrevious();
        $currentStatus = $this->lineHandler->getCurrent();
        $this->lineHandler->persistCurrentStatus();
        $this->logger->info("Checkpoint: Line status handled successfully");        

        if (is_null($oldStatus)) {
            $this->logger->warning('Could not get previous status. Are your storage folder permissions correct?');
            return -1;
        }

        $diff = $this->lineHandler->getDiff($oldStatus, $currentStatus);
        $notifications = $this->notifier->getMessagesFromDiff($diff);
        $hasErrors = false;
        $this->logger->info("Checkpoint: Starting notifications broadcast");  
        foreach($notifications as $notif) {
            $result = $this->notifier->notify($notif);

            if (!$result->getOk()) {
                $this->logger->error("Notification not sent!", (array) $result);
                $hasErrors = true;
                continue;
            }
            
            $this->logger->info("Notification sent success!", (array) $result);
        }

        $this->logger->info(
            sprintf("Checkpoint: All %d notifications sent", count($notifications))
        );  

        return $hasErrors ? 1 : 0;
    }
}
