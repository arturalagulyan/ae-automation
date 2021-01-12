<?php

namespace Renderer\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Renderer\Events\FFMPEGFinished;
use Renderer\Events\FFMPEGStarted;
use Renderer\Events\NexrenderFinished;
use Renderer\Events\NexrenderStarted;
use Renderer\Events\RenderingFinished;
use Renderer\Events\RenderingStarted;
use Renderer\Listeners\LogFFMPEGFinished;
use Renderer\Listeners\LogFFMPEGStarted;
use Renderer\Listeners\LogNexrenderFinished;
use Renderer\Listeners\LogNexrenderStarted;
use Renderer\Listeners\LogRenderingFinished;
use Renderer\Listeners\LogRenderingStarted;

/**
 * Class EventServiceProvider
 * @package Renderer\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $eventListeners = [
        NexrenderStarted::class => [
            LogNexrenderStarted::class
        ],
        NexrenderFinished::class => [
            LogNexrenderFinished::class
        ],
        RenderingStarted::class => [
            LogRenderingStarted::class
        ],
        RenderingFinished::class => [
            LogRenderingFinished::class
        ],
        FFMPEGStarted::class => [
            LogFFMPEGStarted::class
        ],
        FFMPEGFinished::class => [
            LogFFMPEGFinished::class
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->eventListeners as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
