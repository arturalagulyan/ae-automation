<?php

namespace App\Console;

use Illuminate\Console\Command;
use Renderer\Commands\Render\MultiCommand;
use Renderer\Commands\Replicate\Nexrender\NexrenderCommand;
use Renderer\Commands\ReplicateCommand;
use Renderer\Commands\RenderCommand;

/**
 * Class Test
 * @package App\Console
 */
class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'render:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test rendering';

    /**
     * @var MultiCommand
     */
    protected $renderCommand;

    /**
     * @var ReplicateCommand
     */
    protected $nexrenderCommand;

    /**
     * Test constructor.
     * @param MultiCommand $renderCommand
     * @param ReplicateCommand $nexrenderCommand
     */
    public function __construct(MultiCommand $renderCommand, NexrenderCommand $nexrenderCommand)
    {
        parent::__construct();

        $this->renderCommand = $renderCommand;
        $this->nexrenderCommand = $nexrenderCommand;
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $replication = $this->nexrenderCommand->setData([
            'id' => 'test1234',
            'template' => 'nexrender-boilerplate'
        ])->setOptions([
           'options' => [
               '--skip-render',
               '--skip-cleanup',
           ]
        ])->execute();

        $this->renderCommand
            ->setData([
                'id' => $replication['uid'],
                'filename' => 'nexrender-boilerplate-v1-1',
                'composition' => '!FINAL',
                'sequence_n' => 4
            ])->setOptions([
                'frameRate' => 30,
                'startFrame' => '0000',
            ])->execute();

//        $this->renderCommand->setData([
//            'id' => 'test1234',
//            'filename' => 'nexrender-boilerplate-v1-1',
//            'composition' => '!FINAL',
//            'output_extension' => 'mp4',
//        ])->execute();
    }
}
