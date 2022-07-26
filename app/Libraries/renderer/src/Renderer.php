<?php

namespace Renderer;

use Renderer\Steps\Nexrender;
use Renderer\Steps\Rendering;

/**
 * Class Renderer
 * @package Renderer
 */
class Renderer
{
    /**
     * @var Rendering
     */
    protected $rendering;

    /**
     * @var Nexrender
     */
    protected $replication;

    /**
     * Renderer constructor.
     * @param Nexrender $nexrender
     * @param Rendering $rendering
     */
    public function __construct(
        Nexrender $nexrender,
        Rendering $rendering
    )
    {
        $this->rendering = $rendering;
        $this->replication = $nexrender;
    }

    /**
     * @param $options
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process($options)
    {
        $job = $this->replication->setData($options['replication'])->process();

        if (!empty($options['rendering'])) {
            $options['rendering']['id'] = $job['uid'];
            $this->rendering->setData($options['rendering'])->process();
        }
    }
}
