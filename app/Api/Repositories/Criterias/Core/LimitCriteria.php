<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LimitCriteria
 * @package Api\Repositories\Criterias\Core
 */
class LimitCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $limit;

    /**
     * LimitCriteria constructor.
     * @param $limit
     */
    public function __construct($limit)
    {
        $this->limit = $limit;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return $query->limit($this->limit);
    }
}
