<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class InnerJoinCriteria
 * @package Api\Repositories\Criterias\Core
 */
class InnerJoinCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $localColumn;

    /**
     * @var string
     */
    private $foreignColumn;

    /**
     * @var array
     */
    private $foreignSelects;

    /**
     * @var string
     */
    private $foreignTable;

    /**
     * InnerJoinCriteria constructor.
     * @param $table
     * @param $localColumn
     * @param $foreignColumn
     * @param array $foreignSelects
     * @param null $foreignTable
     */
    public function __construct($table, $localColumn, $foreignColumn, $foreignSelects = [], $foreignTable = null)
    {
        $this->table = $table;
        $this->localColumn = $localColumn;
        $this->foreignColumn = $foreignColumn;
        $this->foreignSelects = $foreignSelects;
        $this->foreignTable = $foreignTable;
    }

    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = !empty($this->foreignTable) ? $this->foreignTable : $this->getTable($query);

        if (!empty($this->foreignSelects)) {
            $foreignSelects = [];

            foreach ($this->foreignSelects as $foreignSelect) {
                $foreignSelects[] = "$this->table.$foreignSelect";
            }

            $query = $query->addSelect($foreignSelects);
        }

        return $query->join($this->table, "$this->table.$this->foreignColumn", "=", "$table.$this->localColumn");
    }
}
