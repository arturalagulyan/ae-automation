<?php

namespace Renderer\Listeners;

use Illuminate\Support\Facades\Log;
use Renderer\Events\CommandKilled;

/**
 * Class LogCommandKilled
 * @package Renderer\Listeners
 */
class LogCommandKilled extends BaseListener
{
    /**
     * @param CommandKilled $event
     */
    public function handle(CommandKilled $event)
    {
        $data = $event->getData();

        $title = sprintf('Job: %s Killed', $data['data']['id']);

        Log::channel('renderer')->info($title, [
            'data' => $data['data'],
            'command' => $data['command'],
        ]);
    }
}
