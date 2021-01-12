<?php

namespace Renderer\Listeners;

use Renderer\Events\NexrenderStarted;
use Renderer\Traits\Loggable;

/**
 * Class LogNexrenderStarted
 * @package Renderer\Listeners
 */
class LogNexrenderStarted extends BaseListener
{
    use Loggable;

    /**
     * @param NexrenderStarted $event
     */
    public function handle(NexrenderStarted $event)
    {
        $data = $event->getData();

        $content = 'Replication Started' . PHP_EOL . PHP_EOL;
//        $content .= 'Job: ' . json_encode($data['job'], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['job']['uid'])->log($content);
    }
}
