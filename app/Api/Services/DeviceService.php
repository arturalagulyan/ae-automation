<?php

namespace Api\Services;

use Api\Filters\DeviceFilter;
use Api\Models\BaseApiModel;
use Api\Repositories\Criterias\Core\DataCriteria;
use Api\Repositories\DeviceRepository;
use Api\Transformers\DeviceTransformer;
use Api\Validators\DeviceValidator;
use Illuminate\Support\Arr;

/**
 * Class DeviceService
 * @package Api\Services
 */
class DeviceService extends BaseApiService
{
    /**
     * UserService constructor.
     * @param DeviceFilter $filter
     * @param DeviceValidator $validator
     * @param DeviceRepository $repository
     * @param DeviceTransformer $transformer
     */
    public function __construct(
        DeviceFilter $filter,
        DeviceValidator $validator,
        DeviceRepository $repository,
        DeviceTransformer $transformer
    )
    {
        parent::__construct($filter, $validator, $repository, $transformer);
    }

    /**
     * @param array $data
     * @return \Api\Models\BaseApiModel|null
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ping(array $data): ?BaseApiModel
    {
        $this->validator
            ->setData($data)
            ->validate('ping');

        $criteria = [
            'where' => [
                'name' => $data['dev_name']
            ],
        ];

        $values = [
            'name' => $data['dev_name'],
            'last_pinged_at' => now()->toDateTimeString()
        ];

        if (Arr::get($data, 'dev_mac_id')) {
            $values['mac_id'] = $data['dev_mac_id'];

            $criteria = array_merge($criteria, [
                'orWhere' => [
                    'mac_id' => $data['dev_mac_id']
                ]
            ]);
        }

        $existing = $this->repository->pushCriteria(new DataCriteria($criteria))->first();

        return empty($existing) ? $this->repository->create($values) : $this->repository->update($values, $existing->id);
    }
}
