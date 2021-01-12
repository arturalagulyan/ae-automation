<?php

namespace Renderer\Listeners;

use Renderer\Events\RenderingStarted;
use Renderer\Traits\Loggable;

/**
 * Class LogRenderingStarted
 * @package Renderer\Listeners
 */
class LogRenderingStarted extends BaseListener
{
    use Loggable;

    /**
     * @param RenderingStarted $event
     */
    public function handle(RenderingStarted $event)
    {
        $data = $event->getData();

        $content = 'Rendering Started' . PHP_EOL . PHP_EOL;

        $this->setLogFile($data['id'])->log($content);
    }
}
