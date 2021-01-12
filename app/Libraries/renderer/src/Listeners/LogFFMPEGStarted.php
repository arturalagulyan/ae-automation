<?php

namespace Renderer\Listeners;

use Renderer\Events\FFMPEGStarted;
use Renderer\Traits\Loggable;

/**
 * Class LogFFMPEGStarted
 * @package Renderer\Listeners
 */
class LogFFMPEGStarted extends BaseListener
{
    use Loggable;

    /**
     * @param FFMPEGStarted $event
     */
    public function handle(FFMPEGStarted $event)
    {
        $data = $event->getData();

        $content = 'FFMPEG Started' . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['id'])->log($content);
    }
}
