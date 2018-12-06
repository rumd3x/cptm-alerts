<?php
namespace CptmAlerts\Modules;

use CptmAlerts\Classes\NotificationFactory;
use Monolog\Logger;

class Core
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var StatusHandler
     */
    private $statusHandler;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @param \Monolog\Logger $logger
     * @param string $slackKey
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->notifier = new Notifier();
        $this->statusHandler = new StatusHandler();
        $this->notificationFactory = new NotificationFactory();
    }

    /**
     * @return int
     */
    public function run()
    {
        $this->logger->info("Checkpoint: Application is running");

        $oldStatus = $this->statusHandler->getPrevious();
        $currentStatus = $this->statusHandler->getCurrent();
        $this->statusHandler->persistCurrentStatus();
        $this->logger->info("Checkpoint: Line status handled successfully");

        if (is_null($oldStatus)) {
            $this->logger->warning('Could not get previous status.');
            $this->logger->warning('If this is not your app first run, maybe your storage permissions are incorrect?');
            return -1;
        }

        $diff = $this->statusHandler->getDiff($oldStatus, $currentStatus);
        if ($diff->isEmpty()) {
            $this->logger->info("No status change to notify");
            return 0;
        }

        $notification = $this->notificationFactory->make($diff);

        $this->logger->info("Checkpoint: Starting notifications broadcast");
        $result = $this->notifier->notify($notification);

        if (!$result->getOk()) {
            $this->logger->error("Notification not sent!", (array) $result);
            return 1;
        }

        $this->logger->info("Notification sent success!", (array) $result);
        return 0;
    }
}
