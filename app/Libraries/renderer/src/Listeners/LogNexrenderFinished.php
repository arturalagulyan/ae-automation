<?php

namespace Renderer\Listeners;

use Renderer\Events\NexrenderFinished;
use Renderer\Traits\Loggable;

/**
 * Class LogNexrenderFinished
 * @package Renderer\Listeners
 */
class LogNexrenderFinished extends BaseListener
{
    use Loggable;

    /**
     * @param NexrenderFinished $event
     */
    public function handle(NexrenderFinished $event)
    {
        $data = $event->getData();

        $startedAt = carbon($data['job']['startedAt']);
        $finishedAt = carbon($data['job']['finishedAt']);

        $seconds = $finishedAt->diffInSeconds($startedAt);

        $content = "Replication Finished ($seconds)s" . PHP_EOL . PHP_EOL;
//        $content .= 'Job: ' . json_encode($data['job'], JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['job']['uid'])->log($content);
    }
}
