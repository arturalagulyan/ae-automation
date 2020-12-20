<?php

namespace Renderer\Commands\Traits;

/**
 * Trait Destinations
 * @package Renderer\Commands\Traits
 */
trait Destinations
{
    /**
     * @return string
     */
    protected function getOutputFile()
    {
        $filename = sprintf('%s-%s', $this->data['id'], $this->data['filename']);

        return $this->getOutputFolder() . $filename . '.mp4';
    }

    /**
     * @return string
     */
    protected function getWAVFile()
    {
        return $this->getRenderFolder() . $this->data['filename'] . '.wav';
    }

    /**
     * @return string
     */
    protected function getAEPFile()
    {
        return $this->getReplicateFolder() . $this->data['filename'] . '.aep';
    }

    /**
     * @return string
     */
    protected function getRenderFolder()
    {
        return renderer_conf('render_folder') . $this->data['id'] . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getOutputFolder()
    {
        return renderer_conf('outputs_folder') . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getReplicateFolder()
    {
        return renderer_conf('replicate_folder') . $this->data['id'] . DIRECTORY_SEPARATOR;
    }
}
