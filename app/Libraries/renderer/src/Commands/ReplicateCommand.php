<?php

namespace Renderer\Commands;

use Illuminate\Support\Arr;
use Renderer\Processor;
use Requester\Request;

/**
 * Class ReplicateCommand
 * @package Renderer\Commands
 */
class ReplicateCommand extends BaseCommand
{
    /**
     * @var Request
     */
    protected $requester;

    /**
     * NexrenderCommand constructor.
     * @param Processor $processor
     * @param Request $requester
     */
    public function __construct(
        Processor $processor,
        Request $requester
    )
    {
        parent::__construct($processor);

        $this->requester = $requester;
        $this->requester->setUrl(config('renderer.nexrender.server_url'));
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $this->cloneTemplate();
        $this->modifyOptionsJson();

        if (!$this->runCommand($this->command())) {
            return false;
        }

        if (Arr::get($this->data, 'clean') === true) {
            $this->cleanTemplate();
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getSourcePath(): string
    {
        return $this->config['templates_folder'] . $this->data['template'] . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getDestinationPath(): string
    {
        return $this->config['replicate_folder'] . $this->data['template'] . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getOptionsJson(): string
    {
        return  $this->getDestinationPath() . $this->config['nexrender']['options_json'];
    }

    /**
     * @return bool
     */
    protected function cloneTemplate(): bool
    {
        return renderer_copy_r($this->getSourcePath(), $this->getDestinationPath());
    }

    /**
     * @return bool
     */
    protected function cleanTemplate(): bool
    {
        return renderer_clean_r($this->getDestinationPath());
    }

    /**
     * @return string
     */
    protected function command()
    {
        $command = $this->config['nexrender']['cli'];
        $command .= ' --file ' . $this->getOptionsJson();
        $command .= ' --workpath ' . $this->getDestinationPath();

        foreach ($this->options as $option) {
            $command .= " $option";
        }

        return $command;
    }

    /**
     * @return array
     */
    protected function getOptionsJsonArray(): array
    {
        return json_decode(
            file_get_contents(
                $this->getOptionsJson()
            ),
            true
        );
    }

    /**
     * @return array
     */
    protected function getModifiedOptions(): array
    {
        $options = $this->getOptionsJsonArray();
        $options['template']['src'] = "file:///" . $this->getDestinationPath() . $options['template']['src'];

        if (Arr::get($options, 'actions.postrender')) {
            foreach ($options['actions']['postrender'] as &$action) {
                if (isset($action['input'])) {
                    $action['input'] = $this->getDestinationPath() . $action['input'];
                }
                if (isset($action['input'])) {
                    $action['output'] = $this->getDestinationPath() . $action['output'];
                }
            }
        }

        return $options;
    }

    /**
     * @return bool
     */
    protected function modifyOptionsJson(): bool
    {
        $options = $this->getModifiedOptions();

        $options = json_encode(
            $options,
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        file_put_contents(
            $this->getOptionsJson(),
            $options
        );

        return true;
    }
}
