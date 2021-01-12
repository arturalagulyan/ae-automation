<?php

namespace Renderer\Listeners;

use Renderer\Events\FFMPEGFinished;
use Renderer\Traits\Loggable;

/**
 * Class LogFFMPEGFinished
 * @package Renderer\Listeners
 */
class LogFFMPEGFinished extends BaseListener
{
    use Loggable;

    /**
     * @param FFMPEGFinished $event
     */
    public function handle(FFMPEGFinished $event)
    {
        $data = $event->getData();

        $seconds = $data['seconds'];

        $content = "FFMPEG Finished ($seconds)s" . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['id'])->log($content);
    }
}
