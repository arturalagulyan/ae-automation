<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DoesntHaveCriteria
 * @package Api\Repositories\Criterias\Core
 */
class DoesntHaveCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $relation;

    /**
     * DoesntHaveCriteria constructor.
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
        return $query->doesntHave($this->relation);
    }
}
