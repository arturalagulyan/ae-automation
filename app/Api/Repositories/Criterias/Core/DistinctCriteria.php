<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DistinctCriteria
 * @package Api\Repositories\Criterias\Core
 */
class DistinctCriteria extends BaseApiCriteria
{
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $query->distinct();

        return $query;
    }
}
