<?php

namespace Renderer\Listeners;

use Renderer\Events\RenderingFinished;
use Renderer\Traits\Loggable;

/**
 * Class LogRenderingFinished
 * @package Renderer\Listeners
 */
class LogRenderingFinished extends BaseListener
{
    use Loggable;

    /**
     * @param RenderingFinished $event
     */
    public function handle(RenderingFinished $event)
    {
        $data = $event->getData();

        $seconds = $data['seconds'];

        $content = "Rendering Finished ($seconds)s" . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['id'])->log($content);
    }
}
