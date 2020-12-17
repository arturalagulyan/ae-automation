<?php

namespace Renderer\Commands\Render;

/**
 * Class MultiCommand
 * @package Renderer\Commands\Render
 */
class MultiCommand extends BaseRenderCommand
{
    /**
     * @return bool
     */
    public function execute()
    {
        $this->createRenderFolder();

        if (!$this->executeSeqWavCommands()) {
            return false;
        }

        $this->createOutputFolder();

        if (!$this->executeFFmpegCommand()) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function createRenderFolder()
    {
        if (!file_exists($this->getRenderFolder())) {
            mkdir($this->getRenderFolder(), 0777, true);
        }
    }

    /**
     *
     */
    public function createOutputFolder()
    {
        if (!file_exists($this->getOutputFolder())) {
            mkdir($this->getOutputFolder(), 0777, true);
        }
    }

    /**
     * @return string
     */
    protected function getRenderFolder(): string
    {
        return $this->config['render_folder'] . $this->data['id'] . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getOutputFolder(): string
    {
        return $this->config['outputs_folder'] . $this->data['id'] . DIRECTORY_SEPARATOR;
    }

    /**
     * @return bool
     */
    protected function executeSeqWavCommands()
    {
        $seqCommand = $this->generateSeqCommand();
        $wavCommand = $this->generateWavCommand();

        $n = $this->data['sequence_n'];

        for ($i = 0; $i < $n; $i++) {
            $this->addTask($seqCommand);
        }

        $this->addTask($wavCommand);

        $this->work();

        while ($this->isWorking()) {
            sleep(5);
        }

        $this->checkCorruptedFiles();

        if ($this->isKilled()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkCorruptedFiles()
    {
        $files = glob($this->getRenderFolder() . '*');

        $corrupted = false;

        foreach ($files as $file) {
            if (filesize($file)) {
                continue;
            }

            $corrupted = true;
            unlink($file);
        }

        if (!$corrupted) {
            return true;
        }

        $command = $this->generateSeqCommand();

        return $this->runCommand($command);
    }

    /**
     * @return bool
     */
    protected function executeFFmpegCommand()
    {
        $command = $this->generateFFmpegCommand();

        return $this->runCommand($command);
    }

    /**
     * @return string
     */
    protected function generateSeqCommand()
    {
        $command1 = $this->config['ae'];
        $command1 .= ' -project ' . $this->getAEP();
        $command1 .= ' -comp ' . $this->data['composition'];
        $command1 .= ' -RStemplate multi-best-full';
        $command1 .= ' -OMtemplate jpeg-seq';
        $command1 .= ' -output ' . $this->getRenderFolder() . '[####].jpg';

        return $command1;
    }

    /**
     * @return string
     */
    protected function getAEP()
    {
        return $this->config['replicate_folder'] . $this->data['id'] . DIRECTORY_SEPARATOR . $this->data['filename'] . '.aep';
    }

    /**
     * @return string
     */
    protected function generateWavCommand()
    {
        $command2 = $this->config['ae'];
        $command2 .= ' -project ' . $this->getAEP();
        $command2 .= ' -comp ' . $this->data['composition'];
        $command2 .= ' -OMtemplate "wav-audio"';
        $command2 .= ' -output ' . $this->getRenderFolder() . $this->data['filename'] . '.wav';

        return $command2;
    }

    /**
     * @return string
     */
    protected function generateFFmpegCommand()
    {
        $codec = '-c:v libx264 -b:v 16000k -c:a aac -strict experimental -b:a 128k -pix_fmt yuv420p';

        $command3 = $this->config['ffmpeg'];
        $command3 .= ' -r ' . $this->options['frameRate'];
        $command3 .= ' -f image2';
        $command3 .= ' -start_number ' . $this->options['startFrame'];
        $command3 .= ' -i ' . $this->getRenderFolder() . '%04d.jpg';
        $command3 .= ' ' . $this->getOutputFolder() . $this->data['filename'] . '.mp4';
        $command3 .= ' ' . $codec;

        return $command3;
    }
}
