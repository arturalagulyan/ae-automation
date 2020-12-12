<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DataCriteria
 * @package Api\Repositories\Criterias\Core
 */
class DataCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * DataCriteria constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $this->repository = $repository;

        $this->buildData($query, $this->data);

        return $query;
    }
}
