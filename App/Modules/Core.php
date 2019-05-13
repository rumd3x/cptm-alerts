<?php
namespace CptmAlerts\Modules;

use Exception;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Rumd3x\Slack\Notifier as SlackNotifier;
use CptmAlerts\Modules\Notification\Notifier;
use CptmAlerts\Modules\Notification\Factory\SlackNotificationFactory;

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
        $this->timeTracker = new TimeTracker();
        $this->logger = new Logger('CPTM Alerts by Rumd3x');
        // $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../Storage/Logs/' .  date('Y-m-d') . '-app.log'));
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../Storage/Logs/app.log'));
        $this->logger->info('Loading assets.');

        $dotenv = new Dotenv(__DIR__ . '/../..');
        $dotenv->load();
        $dotenv->required('SLACK_KEY')->notEmpty();
        $dotenv->required('SLACK_CHANNEL')->notEmpty();
        $dotenv->required('NOTIFY_LEVEL')->isInteger();
        $dotenv->required('NOTIFY_DAYS')->notEmpty();
        $dotenv->required('NOTIFY_LINES')->notEmpty();

        $this->statusHandler = (new StatusHandler())->pushLogHandler($this->logger);
        $this->notifier = new Notifier();
    }

    public function init()
    {
        $this->logger->info('Initializing.');

        $this->notifier->addChannel(new SlackNotificationFactory, new SlackNotifier(getenv('SLACK_KEY')));

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
        $this->logger->info("Checkpoint: Program started");

        $oldStatus = $this->statusHandler->getPrevious();
        $currentStatus = $this->statusHandler->getCurrent();
        $this->statusHandler->persistCurrentStatus();
        $this->logger->info("Checkpoint: Line status handled successfully");

        if (is_null($oldStatus)) {
            $this->logger->warning('Could not get previous status.');
            $this->logger->warning('If this is not your app first run, maybe your storage permissions are incorrect?');
            return -1;
        }

        $diff = $this->statusHandler->getAllDiff($oldStatus, $currentStatus);
        if ($diff->isEmpty()) {
            $this->logger->info("No status change to notify");
            return 0;
        }

        if (!$this->shouldNotifyToday()) {
            $this->logger->info("Notifications disabled for today");
            return 1;
        }

        $this->logger->info("Checkpoint: Starting notifications broadcast");
        $result = $this->notifier->notify($diff);

        $this->logger->info("Notification sent success!", (array) $result);
        return 0;
    }

    /**
     * @return bool
     */
    private function shouldNotifyToday()
    {
        $setting = getenv('NOTIFY_DAYS');
        if (strtolower(trim($setting)) === 'all') {
            return true;
        }

        $today = Carbon::today()->weekday();
        $daysToNotify = explode(',', $setting);
        return in_array($today, $daysToNotify);
    }
}
