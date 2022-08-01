<?php

namespace Renderer\Listeners;

use Renderer\Events\NexrenderFailed;
use Renderer\Traits\Loggable;

/**
 * Class LogNexrenderFailed
 * @package Renderer\Listeners
 */
class LogNexrenderFailed extends BaseListener
{
    use Loggable;

    /**
     * @param NexrenderFailed $event
     */
    public function handle(NexrenderFailed $event)
    {
        $data = $event->getData();

        $content = "Replication Failed" . PHP_EOL . PHP_EOL;
        $content .= 'Job: ' . json_encode($data['job'], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['job']['uid'])->log($content);
    }
}
