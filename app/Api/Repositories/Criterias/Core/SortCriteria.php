<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SortCriteria
 * @package Api\Repositories\Criterias\Core
 */
class SortCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $table;

    /**
     * SortCriteria constructor.
     * @param $column
     * @param string $order
     * @param null $table
     */
    public function __construct($column, $order = 'asc', $table = null)
    {
        $this->order = $order;
        $this->column = $column;
        $this->table = $table;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->table ? $this->table : $this->getTable($query);

        return $query->orderBy("$table.$this->column", $this->order);
    }
}
