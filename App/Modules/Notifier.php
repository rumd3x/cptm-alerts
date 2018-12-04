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

    public function __construct() {
        $this->slack = Slack::create(getenv('SLACK_KEY'));
    }

    public function send(array $data) {
        $data = array_merge($data, [
            'channel' => getenv('SLACK_CHANNEL'),
            'username' => getenv('SLACK_BOT_USERNAME')
        ]);
        return $this->slack->chatPostMessage($data);
    }
    
    /**
     * @param String $message
     * @return bool
     */
    public function message(String $message) {
        return $this->send(['text' => $message]);
    }

    public function notify(Notification $notification)
    {
        return $this->send($notification->toAttachments());
    }

    public function getMessagesFromDiff(array $diff) {
        return [];
    }
}