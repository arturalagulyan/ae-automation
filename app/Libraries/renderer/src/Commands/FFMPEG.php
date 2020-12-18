<?php

namespace Renderer\Commands;

use Renderer\Commands\Traits\Destinations;

/**
 * Class FFMPEG
 * @package Renderer\Commands
 */
class FFMPEG extends BaseCommand
{
    use Destinations;

    /**
     * @return string
     */
    public function command()
    {
        $codec = '-c:v libx264 -b:v 16000k -c:a aac -strict experimental -b:a 128k -pix_fmt yuv420p';

        $command = renderer_conf('ffmpeg');
//        $command .= ' -r ' . $this->options['frameRate'];
//        $command .= ' -f image2';
//        $command .= ' -start_number ' . $this->options['startFrame'];
        $command .= ' -i ' . $this->getRenderFolder() . '%04d.jpg';
        $command .= ' ' . $this->getOutputFile();
        $command .= ' ' . $codec;

        return $command;
    }
}
