<?php

namespace App\Console;

use Illuminate\Console\Command;
use Renderer\Renderer;

/**
 * Class Render
 * @package App\Console
 */
class Render extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'render {--project=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test rendering';

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * Test constructor.
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        parent::__construct();

        $this->renderer = $renderer;
    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->renderer->process($this->getConfig());
    }

    /**
     * @return mixed
     */
    protected function getConfig()
    {
        $projectsFolder = renderer_conf('projects_folder');

        $json = $projectsFolder . $this->option('project') . '.json';

        if (!file_exists($json)) {
            throw new \Exception('Wrong project name');
        }

        return json_decode(file_get_contents($json), true);
    }
}
