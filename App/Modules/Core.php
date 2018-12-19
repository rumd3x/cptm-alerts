<?php
namespace CptmAlerts\Modules;

use CptmAlerts\Classes\NotificationFactory;
use Monolog\Logger;

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;

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
     * @var TimeTracker
     */
    private $timeTracker;

    public function __construct()
    {
        $this->logger = new Logger('CPTM Alerts');
        $this->notifier = new Notifier();
        $this->timeTracker = new TimeTracker();
        $this->statusHandler = new StatusHandler();
        $this->notificationFactory = new NotificationFactory();
    }

    public function init()
    {
        $logFilename = date('Y-m-d') . '-app.log';
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../Storage/Logs/' . $logFilename));
        $this->logger->info('Application initializing.');
        $dotenv = new Dotenv(__DIR__ . '/../..');
        $dotenv->load();
        $dotenv->required('SLACK_KEY')->notEmpty();
        $dotenv->required('SLACK_CHANNEL')->notEmpty();

        try {
            $returnCode = $this->run();
            $this->logger->info(sprintf("Application executed gracefully! Exit code: %d in %f seconds.", $returnCode, $this->timeTracker->getElapsed()));
        } catch (Exception $e) {
            $this->logger->error(
                sprintf("Runtime Error in %f seconds", $this->timeTracker->getElapsed()),
                [
                    'Message' => $e->getMessage(),
                    'File' => $e->getFile(),
                    'Line' => $e->getLine(),
                    'Code' => $e->getCode(),
                    'Trace' => $e->getTraceAsString(),
                ]
            );
        }
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
