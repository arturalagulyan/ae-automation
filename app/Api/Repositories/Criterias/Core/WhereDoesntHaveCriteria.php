<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereDoesntHaveCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereDoesntHaveCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $where;

    /**
     * @var
     */
    protected $relation;

    /**
     * WhereDoesntHaveCriteria constructor.
     * @param $relation
     * @param array $where
     */
    public function __construct($relation, $where = [])
    {
        $this->where = $where;
        $this->relation = $relation;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return $query->whereDoesntHave($this->relation, function (Builder $relatedQuery) use ($query) {
            $relatedQuery->where($this->where);
//            foreach ($this->where as $column => $values) {
//                if (!is_array($values)) {
//                    $values = [
//                        $values
//                    ];
//                }
//
//                $relatedTable = $this->getRelationTable($query, $this->relation);
//
//                $relatedQuery
//                    ->select("$relatedTable.$column")
//                    ->whereIn("$relatedTable.$column", $values);
//            }
        });
    }
}
