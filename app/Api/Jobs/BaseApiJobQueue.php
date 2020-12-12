<?php

namespace Api\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseApiJobQueue
 * @package Api\Jobs
 */
abstract class BaseApiJobQueue extends BaseApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int|null
     */
    protected $authUserId;

    /**
     * BaseApiJobQueue constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->authUserId = Auth::id();

        parent::__construct($data);
    }
}
