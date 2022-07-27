<?php

namespace Api\Filters;

use Api\Repositories\Criterias\Core\DataCriteria;

/**
 * Class DeviceFilter
 * @package Api\Filters
 */
class DeviceFilter extends BaseApiFilter
{
    /**
     * @param $value
     * @throws \Exception
     */
    protected function filterBySearch($value)
    {
        $this->repository->pushCriteria(new DataCriteria([
            'where' => [
                [
                    'field' => 'name',
                    'operator' => 'LIKE',
                    'value' => "%$value%"
                ]
            ],
            'orWhere' => [
                [
                    'field' => 'mac_id',
                    'operator' => 'LIKE',
                    'value' => "%$value%"
                ]
            ]
        ]));
    }

    /**
     * @param string $order
     * @throws \Exception
     */
    protected function sortByName(string $order = 'asc')
    {
        $this->repository->pushCriteria(new DataCriteria([
            'sortBy' => 'name',
            'order' => $order
        ]));
    }

    /**
     * @param string $order
     * @throws \Exception
     */
    protected function sortByMacId(string $order = 'asc')
    {
        $this->repository->pushCriteria(new DataCriteria([
            'sortBy' => 'mac_id',
            'order' => $order
        ]));
    }

    /**
     * @param string $order
     * @throws \Exception
     */
    protected function sortByLastPingedAt(string $order = 'asc')
    {
        $this->repository->pushCriteria(new DataCriteria([
            'sortBy' => 'last_pinged_at',
            'order' => $order
        ]));
    }
}
