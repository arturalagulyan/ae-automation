<?php

namespace Api\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Class BaseApiNotification
 * @package Api\Notifications
 */
abstract class BaseApiNotification extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    protected $data;

    /**
     * BaseApiNotification constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
