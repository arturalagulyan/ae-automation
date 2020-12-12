<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SelectCriteria
 * @package Api\Repositories\Criterias\Core
 */
class SelectCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $columns;

    /**
     * SelectCriteria constructor.
     * @param array $columns
     */
    public function __construct($columns = [])
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

        $tableColumns = [];

        foreach ($this->columns as $column) {
            $tableColumns[] = "$table.$column";
        }

        return $query->addSelect($tableColumns);
    }
}
