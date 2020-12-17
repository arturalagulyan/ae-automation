<?php

namespace Renderer\Commands;

use Renderer\Processor;

/**
 * Class RenderCommand
 * @package Renderer\Commands
 */
class RenderCommand extends BaseCommand
{
    /**
     * RenderCommand constructor.
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct($processor);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function execute()
    {
        $this->initialize();

        $this->createRenderFolder();

        if (!$this->checkAEPFileExistence()) {
            return false;
        }

        if (!$this->runCommand($this->command())) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getRenderedFile(): string
    {
        return $this->renderFolder . $this->data['filename'] . '.' . $this->data['output_extension'];
    }

    /**
     * @return string
     */
    protected function command(): string
    {
        $command = config('renderer.ae');
        $command .= ' -project {{sharedFolder}}{{filename}}.aep -comp "{{composition}}"';
        $command .= ' -output "{{renderFolder}}{{filename}}.{{extension}}"';
//        $command .= ' -RStemplate "{{rsTemplate}}" -OMtemplate "{{omTemplate}}"';

//        $command = str_replace('{{rsTemplate}}', $this->data['rs'], $command);
//        $command = str_replace('{{omTemplate}}', $this->data['om'], $command);
        $command = str_replace('{{filename}}', $this->data['filename'], $command);
        $command = str_replace('{{sharedFolder}}', $this->sharedFolder, $command);
        $command = str_replace('{{renderFolder}}', $this->renderFolder, $command);
        $command = str_replace('{{composition}}', $this->data['composition'], $command);
        $command = str_replace('{{extension}}', $this->data['output_extension'], $command);

        return $command;
    }

    /**
     *
     */
    public function createRenderFolder()
    {
        if (!file_exists($this->renderFolder)) {
            mkdir($this->renderFolder, 0777, true);
        }
    }

    /**
     * @param null $directory
     * @return bool
     */
    public function removeRenderFolder($directory = null): bool
    {
        $directory = (is_null($directory)) ? $this->renderFolder : $directory;


        if (!file_exists($directory)) {
            return true;
        }

        if (!is_dir($directory)) {
            return unlink($directory);
        }

        foreach (scandir($directory) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->removeRenderFolder($directory . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($directory);
    }

    /**
     * @return bool
     */
    protected function checkAEPFileExistence(): bool
    {
        if (! is_dir($this->sharedFolder)) {
            return false;
        }

        $attempts = 1;
        $fullPath = $this->sharedFolder . $this->data['filename'] . '.aep';

        while ((! file_exists($fullPath)) && ($attempts <= 3)) {
            $attempts++;
            sleep(5);
        }

        if (! file_exists($fullPath)) {
            return false;
        }

        return true;
    }
}
