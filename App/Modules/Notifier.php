<?php
namespace CptmAlerts\Modules;

use Exception;
use Carbon\Carbon;
use CptmAlerts\Classes\SlackNotification;
use JoliCode\Slack\ClientFactory as Slack;

class Notifier
{
    /**
     * @var \JoliCode\Slack\Api\Client
     */
    private $slack;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct()
    {
        $this->slack = Slack::create(getenv('SLACK_KEY'));
    }

    /**
     * @param SlackNotification $notification
     * @return JoliCode\Slack\Api\Model\ChatPostMessagePostResponse200
     */
    public function notify(SlackNotification $notification)
    {
        $retorno = $this->slack->chatPostMessage($notification->toArray());
        if (!$retorno->getOk()) {
            $errorMessage = sprintf("%s - %s", "Notification not sent!", json_encode((array) $retorno));
            throw new Exception($errorMessage);
        }
        return $retorno;
    }

    /**
     * @return bool
     */
    public function shouldNotifyToday()
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
