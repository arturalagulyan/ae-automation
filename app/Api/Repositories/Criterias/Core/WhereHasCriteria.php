<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereHasCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereHasCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $where;

    /**
     * @var
     */
    protected $options;

    /**
     * @var
     */
    protected $relation;

    /**
     * WhereHasCriteria constructor.
     * @param $relation
     * @param array $where
     * @param array $options
     */
    public function __construct($relation, $where = [], $options = [])
    {
        $this->where = $where;
        $this->options = $options;
        $this->relation = $relation;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return $query->whereHas($this->relation, function (Builder $relatedQuery) use ($query) {
            $relatedQuery->where($this->where);

            if (!empty($this->options)) {
                $this->buildData($relatedQuery, $this->options);
            }
        });
    }
}
