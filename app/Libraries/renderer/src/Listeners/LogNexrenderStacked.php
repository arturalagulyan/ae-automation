<?php

namespace Renderer\Listeners;

use Renderer\Events\NexrenderStacked;
use Renderer\Traits\Loggable;

/**
 * Class LogNexrenderStacked
 * @package Renderer\Listeners
 */
class LogNexrenderStacked extends BaseListener
{
    use Loggable;

    /**
     * @param NexrenderStacked $event
     */
    public function handle(NexrenderStacked $event)
    {
        $data = $event->getData();

        $content = "Replication Stacked" . PHP_EOL . PHP_EOL;
        $content .= 'Job: ' . json_encode($data['job'], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['job']['uid'])->log($content);
    }
}
