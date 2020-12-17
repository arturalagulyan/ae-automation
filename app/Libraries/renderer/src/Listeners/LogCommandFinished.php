<?php

namespace Renderer\Listeners;

use Illuminate\Support\Facades\Log;
use Renderer\Events\CommandFinished;

/**
 * Class LogCommandFinished
 * @package Renderer\Listeners
 */
class LogCommandFinished extends BaseListener
{
    /**
     * @param CommandFinished $event
     */
    public function handle(CommandFinished $event)
    {
        $data = $event->getData();

        $title = sprintf('Job: %s Finished', $data['data']['id']);

        Log::channel('renderer')->info($title, [
            'data' => $data['data'],
            'command' => $data['command'],
        ]);
    }
}
