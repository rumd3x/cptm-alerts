<?php
namespace CptmAlerts\Modules\Notification;

use Exception;
use Carbon\Carbon;
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
     */
    public function notify(Collection $diffCollection)
    {
        if (!$this->shouldNotifyToday()) {
            throw new Exception('Notifications disabled for today');
        }

        foreach ($this->channels as $ch) {
            $notification = $ch->get('factory')->make($diff);
            $retorno = $ch->get('sender')->notify($notification);
            if (!$retorno->getOk()) {
                $errorMessage = sprintf("%s - %s", "Notification not sent!", json_encode((array) $retorno));
                throw new Exception($errorMessage);
            }
        }
    }

    /**
     * @param NotificationFactoryInterface $factory
     * @return self
     */
    public function addChannel(NotificationFactoryInterface $factory, $sender)
    {
        $channel = collect();
        $channel->put('factory', $factory);
        $channel->put('sender', $sender);
        $this->channels->push($channel);
        return $this;
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
