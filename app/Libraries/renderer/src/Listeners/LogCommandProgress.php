<?php

namespace Renderer\Listeners;

use Illuminate\Support\Facades\File;
use Renderer\Events\CommandProgress;

/**
 * Class LogCommandProgress
 * @package Renderer\Listeners
 */
class LogCommandProgress extends BaseListener
{
    /**
     * @param CommandProgress $event
     */
    public function handle(CommandProgress $event)
    {
        $data = $event->getData();
        $path = config('renderer.logs_folder');
        $file = $path . $data['data']['id'] . '.log';

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true);
        }
        if (!File::exists($file)) {
            touch($file);
        }

        File::append($file, $data['line']);
    }
}
