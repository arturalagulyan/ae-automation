<?php

namespace App\Console;

use Illuminate\Console\Command;
use Renderer\Renderer;

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
        $this->renderer->process([
            'replication' => [
                'template' => 'nexrender-boilerplate',
                'options' => [
                    'worker' => [
                        '--skip-render',
                        '--skip-cleanup',
                    ]
                ]
            ],
            'rendering' => [
                'composition' => '!FINAL',
                'filename' => 'nexrender-boilerplate-v1-1',
                'options' => [
                    'wav' => [
                        '-OMtemplate "wav-audio"'
                    ],
                    'ffmpeg' => [
                        '-r 30',
                        '-start_number 0000',
                        '-f image2',
                    ],
                    'seq' => [
                        '-RStemplate multi-best-full',
                        '-OMtemplate jpeg-seq'
                    ]
                ]
            ]
        ]);
    }
}
