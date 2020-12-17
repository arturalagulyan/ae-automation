<?php

namespace Renderer\Commands\Replicate\Nexrender;

use Illuminate\Support\Arr;
use Renderer\Commands\Replicate\BaseReplicateCommand;
use Renderer\Processor;
use Requester\Request;

/**
 * Class NexrenderCommand
 * @package Renderer\Commands\Replicate
 */
class NexrenderCommand extends BaseReplicateCommand
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
        $this->requester->setUrl(config('renderer.nexrender.api_url'));
    }

    /**
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute()
    {
        $this->addTask($this->workerCommand());
        $this->work();

        $job = $this->requester
            ->setParams($this->getModifiedOptions())
            ->addHeader('nexrender-secret', $this->config['nexrender']['secret'])
            ->post('jobs');

        if ($job->isFailure()) {
            $this->stop();
            return null;
        }

        $job = $job->data();

        while (true) {
            sleep(3);

            $status = $this->requester
                ->addHeader('nexrender-secret', $this->config['nexrender']['secret'])
                ->get('jobs/' . $job['uid']);

            if ($status->isFailure()) {
                $this->stop();
                return null;
            }

            $status = $status->data();

            if ($status['state'] === 'finished' || $status['state'] === 'error') {
                break;
            }
        }

        return $job;
    }

    /**
     * @return string
     */
    protected function getOptionsJson(): string
    {
        return  $this->getSourcePath() . $this->config['nexrender']['options_json'];
    }

    /**
     * @return string
     */
    protected function getSourcePath(): string
    {
        return $this->config['templates_folder'] . $this->data['template'] . DIRECTORY_SEPARATOR;
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
        $options['template']['src'] = "file:///" . $this->getSourcePath() . $options['template']['src'];

        if (Arr::get($options, 'actions.postrender')) {
            foreach ($options['actions']['postrender'] as &$action) {
                if (isset($action['input'])) {
                    $action['input'] = $this->getSourcePath() . $action['input'];
                }
                if (isset($action['input'])) {
                    $action['output'] = $this->getSourcePath() . $action['output'];
                }
            }
        }

        if (Arr::get($this->options, 'config')) {
            $options = array_merge_recursive($options, $this->options['config']);
        }

        return $options;
    }

    /**
     * @return string
     */
    protected function workerCommand(): string
    {
        $command = $this->config['nexrender']['worker'];
        $command .= " --host=" . $this->config['nexrender']['server_url'];
        $command .= " --secret=" . $this->config['nexrender']['secret'];
        $command .= " --workpath=" . $this->config['replicate_folder'];

        if (Arr::get($this->options, 'options')) {
            foreach ($this->options['options'] as $option) {
                $command .= " $option";
            }
        }

        return $command;
    }
}
