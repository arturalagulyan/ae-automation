<?php

namespace Renderer\Steps;

use Illuminate\Support\Arr;
use Renderer\Commands\StartWorker;
use Requester\Request;

/**
 * Class Nexrender
 * @package Renderer\Steps
 */
class Nexrender extends BaseStep
{
    /**
     * @var Request
     */
    protected $requester;

    /**
     * @var StartWorker
     */
    protected $startWorkerCommand;

    /**
     * Nexrender constructor.
     * @param Request $requester
     * @param StartWorker $startWorker
     */
    public function __construct(
        Request $requester,
        StartWorker $startWorker
    )
    {
        $this->startWorkerCommand = $startWorker;

        $this->requester = $requester;
        $this->requester->setUrl(config('renderer.nexrender.api_url'));
    }

    /**
     * @return mixed|null|\Requester\Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process()
    {
        $this->startWorker();

        $job = $this->runJob();

        $this->stopWorker();

        return $job;
    }

    /**
     *
     */
    protected function startWorker()
    {
        if (Arr::get($this->data, 'options.worker')) {
            $this->startWorkerCommand->setOptions($this->data['options']['worker']);
        }

        $this->startWorkerCommand->runAsync();
    }

    /**
     *
     */
    protected function stopWorker()
    {
        $this->startWorkerCommand->killAsync();
    }

    /**
     * @return mixed|null|\Requester\Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function runJob()
    {
        $job = $this->startJob();

        while (true) {
            sleep(3);

            $job = $this->statusJob($job['uid']);

            if ($job['state'] === 'finished' || $job['state'] === 'error') {
                break;
            }
        }

        return $job;
    }

    /**
     * @return mixed|null
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function startJob()
    {
        $job = $this->requester
            ->setParams($this->getModifiedOptions())
            ->addHeader(
                'nexrender-secret',
                renderer_conf('nexrender.secret')
            )
            ->post('jobs');

        if ($job->isFailure()) {
            return null;
        }

        return $job->data();
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function statusJob($id)
    {
        $job = $this->requester
            ->addHeader(
                'nexrender-secret',
                renderer_conf('nexrender.secret')
            )
            ->get('jobs/' . $id);

        if ($job->isFailure()) {
            return null;
        }

        return $job->data();
    }

    /**
     * @return array
     */
    protected function getModifiedOptions()
    {
        $options = $this->getOptionsJsonArray();

        if (Arr::get($this->data, 'json')) {
            $options = array_merge_recursive($options, $this->data['json']);
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getOptionsJsonArray()
    {
        return json_decode(
            file_get_contents(
                $this->getOptionsJson()
            ),
            true
        );
    }

    /**
     * @return string
     */
    protected function getOptionsJson()
    {
        return  $this->getSourcePath() . renderer_conf('nexrender.options_json');
    }

    /**
     * @return string
     */
    protected function getSourcePath()
    {
        return renderer_conf('templates_folder') . $this->data['template'] . DIRECTORY_SEPARATOR;
    }
}
