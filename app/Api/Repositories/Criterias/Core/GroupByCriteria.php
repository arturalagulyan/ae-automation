<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class GroupByCriteria
 * @package Api\Repositories\Criterias\Core
 */
class GroupByCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $columns;

    /**
     * GroupByCriteria constructor.
     * @param $columns
     */
    public function __construct($columns)
    {
        if (!is_array($columns)) {
            $columns = [
                $columns
            ];
        }

        $this->columns = $columns;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->getTable($query);

        foreach ($this->columns as $column) {
            $query = $query->groupBy("$table.$column");
        }

        return $query;
    }
}
