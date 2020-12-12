<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class RelationCriteria
 * @package Api\Repositories\Criterias\Core
 */
class RelationCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $relations;

    /**
     * RelationCriteria constructor.
     * @param array $relations
     */
    public function __construct(array $relations)
    {
        $this->relations = $relations;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $this->repository = $repository;

        $this->buildRelations($query, $this->relations);

        return $query;
    }
}
