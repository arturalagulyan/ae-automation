<?php

namespace Api\Repositories;

use Api\Models\Device;
use Api\Repositories\Criterias\Core\DataCriteria;

/**
 * Class DeviceRepository
 * @package Api\Repositories
 */
class DeviceRepository extends BaseApiRepository
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Device::class;
    }

    /**
     * @return BaseApiRepository|DeviceRepository
     * @throws \Exception
     */
    public function withActive()
    {
        $expires = config('api.devices.expires');

        return $this->pushCriteria(new DataCriteria([
            'where' => [
                [
                    'field' => 'last_pinged_at',
                    'comparison' => '>=',
                    'value' => now()->subMilliseconds($expires)->toDateTimeString()
                ]
            ]
        ]));
    }
}
