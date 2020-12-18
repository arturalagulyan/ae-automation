<?php

namespace Renderer\Commands\Traits;

use Renderer\Commands\BaseCommand;

/**
 * Class StartWorker
 * @package Renderer\Commands\Traits
 */
class StartWorker extends BaseCommand
{
    /**
     * @return string
     */
    public function command(): string
    {
        $command = renderer_conf('nexrender.worker');
        $command .= " --host=" . renderer_conf('nexrender.server_url');
        $command .= " --secret=" . renderer_conf('nexrender.secret');
        $command .= " --workpath=" . renderer_conf('replicate_folder');

        return $command;
    }
}
