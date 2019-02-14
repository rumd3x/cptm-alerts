<?php
namespace CptmAlerts\Modules\Notification;

use Exception;
use Carbon\Carbon;
use Rumd3x\Standards\NotifierInterface;
use Tightenco\Collect\Support\Collection;
use CptmAlerts\Modules\Notification\Factory\NotificationFactoryInterface;

class Notifier
{
    /**
     * @var Collection
     */
    private $channels;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct()
    {
        $this->channels = collect();
    }

    /**
     * @param Collection $notification
     * @return void
     * @throws Exception
     */
    public function notify(Collection $diffCollection)
    {
        foreach ($this->channels as $ch) {
            $notification = $ch->get('factory')->make($diffCollection);
            $retorno = $ch->get('sender')->notify($notification);
            if (!$retorno->getOk()) {
                $errorMessage = sprintf("%s - %s", "Notification not sent!", json_encode((array) $retorno));
                throw new Exception($errorMessage);
            }
        }
    }

    /**
     * @param NotificationFactoryInterface $factory
     * @param NotifierInterface $sender
     * @return self
     */
    public function addChannel(NotificationFactoryInterface $factory, NotifierInterface $sender)
    {
        $channel = collect();
        $channel->put('factory', $factory);
        $channel->put('sender', $sender);
        $this->channels->push($channel);
        return $this;
    }
}
