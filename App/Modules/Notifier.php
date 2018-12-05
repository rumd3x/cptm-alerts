<?php
namespace CptmAlerts\Modules;

use CptmAlerts\Classes\Notification;
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
     * @param Notification $notification
     * @return JoliCode\Slack\Api\Model\ChatPostMessagePostResponse200
     */
    public function notify(Notification $notification)
    {
        $retorno = $this->slack->chatPostMessage($notification->toArray());
        return $retorno;
    }
}
