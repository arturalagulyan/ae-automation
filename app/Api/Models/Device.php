<?php

namespace Api\Models;

/**
 * Class Device
 * @package Api\Models
 */
class Device extends BaseApiModel
{
    /**
     * @var string
     */
    protected $table = 'devices';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'mac_id',
        'last_pinged_at',
    ];
}
