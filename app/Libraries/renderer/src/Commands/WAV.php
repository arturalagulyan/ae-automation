<?php

namespace Renderer\Commands;

use Renderer\Commands\Traits\Destinations;

/**
 * Class WAV
 * @package Renderer\Commands
 */
class WAV extends BaseCommand
{
    use Destinations;

    /**
     * @return string
     */
    public function command()
    {
        $command = renderer_conf('ae');
        $command .= ' -project ' . $this->getAEPFile();
        $command .= ' -comp ' . $this->data['composition'];
//        $command .= ' -OMtemplate "wav-audio"';
        $command .= ' -output ' . $this->getWAVFile();

        return $command;
    }
}
