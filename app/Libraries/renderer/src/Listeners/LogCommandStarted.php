<?php

namespace Renderer\Listeners;

use Illuminate\Support\Facades\Log;
use Renderer\Events\CommandStarted;

/**
 * Class LogCommandStarted
 * @package Renderer\Listeners
 */
class LogCommandStarted extends BaseListener
{
    /**
     * @param CommandStarted $event
     */
    public function handle(CommandStarted $event)
    {
        $data = $event->getData();

        $title = sprintf('Job: %s Started', $data['data']['id']);

        Log::channel('renderer')->info($title, [
            'data' => $data['data'],
            'command' => $data['command'],
        ]);
    }
}
