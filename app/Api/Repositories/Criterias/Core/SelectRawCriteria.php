<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class SelectRawCriteria
 * @package Api\Repositories\Criterias\Core
 */
class SelectRawCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $columns;

    /**
     * SelectRawCriteria constructor.
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
        $tableColumns = [];

        foreach ($this->columns as $column) {
            $tableColumns[] = DB::raw($column);
        }

        return $query->addSelect($tableColumns);
    }
}
