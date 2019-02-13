<?php

namespace CptmAlerts\Modules\Notification\Factory;

use Tightenco\Collect\Support\Collection;

interface NotificationFactoryInterface
{
    /**
     * @param \Tightenco\Collect\Support\Collection of LineStatusDiff $diffCollection
     * @return SlackNotification
     */
    public function make(Collection $diffCollection);
}
