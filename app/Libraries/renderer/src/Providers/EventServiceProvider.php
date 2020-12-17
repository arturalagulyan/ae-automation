<?php

namespace Renderer\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Renderer\Events\CommandFinished;
use Renderer\Events\CommandKilled;
use Renderer\Events\CommandProgress;
use Renderer\Events\CommandStarted;
use Renderer\Listeners\LogCommandFinished;
use Renderer\Listeners\LogCommandKilled;
use Renderer\Listeners\LogCommandProgress;
use Renderer\Listeners\LogCommandStarted;

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
        CommandStarted::class => [
            LogCommandStarted::class
        ],
        CommandProgress::class => [
            LogCommandProgress::class
        ],
        CommandFinished::class => [
            LogCommandFinished::class
        ],
        CommandKilled::class => [
            LogCommandKilled::class
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
