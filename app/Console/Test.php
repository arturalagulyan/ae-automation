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
    protected $signature = 'render:test {--project=}';

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
     * @var array[]
     */
    protected $configPaparazzi = [
        'replication' => [
            'template' => 'paparazzi-main',
            'options' => [
                'worker' => [
                    '--skip-render',
                    '--skip-cleanup',
                    '--aerender-parameter "close SAVE_CHANGES"',
                    '--no-license',
                ]
            ]
        ],
        'rendering' => [
            'composition' => '!FINAL',
            'filename' => 'paparazzi-v1-1',
            'sequence_n' => 7,
            'options' => [
                'wav' => [
                    '-OMtemplate "wav-audio"',
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
    ];

    /**
     * @var array[]
     */
    protected $configBoilerplate = [
        'replication' => [
            'template' => 'nexrender-boilerplate',
            'options' => [
                'worker' => [
                    '--skip-render',
                    '--skip-cleanup',
                    '--aerender-parameter "close SAVE_CHANGES"',
                    '--no-license',
                ]
            ]
        ],
        'rendering' => [
            'composition' => '!FINAL',
            'filename' => 'nexrender-boilerplate-v1-1',
            'sequence_n' => 12,
            'options' => [
                'wav' => [
                    '-OMtemplate "wav-audio"',
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
    ];

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
        if ($this->option('project') === 'paparazzi') {
            $this->renderer->process($this->configPaparazzi);
        } elseif ($this->option('project') === 'boilerplate') {
            $this->renderer->process($this->configBoilerplate);
        } else {
            $this->error('Wrong project name');
        }
    }
}
