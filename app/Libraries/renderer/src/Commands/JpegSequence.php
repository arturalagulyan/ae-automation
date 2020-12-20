<?php

namespace Renderer\Commands;

use Renderer\Commands\Traits\Destinations;

/**
 * Class JpegSequence
 * @package Renderer\Commands
 */
class JpegSequence extends BaseCommand
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
        $command .= ' -output ' . $this->getRenderFolder() . '[####].jpg';

        return $command;
    }
}
