<?php

namespace Api\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class BaseApiNotificationQueue
 * @package Api\Notifications
 */
abstract class BaseApiNotificationQueue extends BaseApiNotification implements ShouldQueue
{
}
