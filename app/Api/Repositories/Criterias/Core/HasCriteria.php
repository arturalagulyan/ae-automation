<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class HasCriteria
 * @package Api\Repositories\Criterias\Core
 */
class HasCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $relation;

    /**
     * HasCriteria constructor.
     * @param $relation
     */
    public function __construct($relation)
    {
        $this->relation = $relation;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return $query->has($this->relation);
    }
}
