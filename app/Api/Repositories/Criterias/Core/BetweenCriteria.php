<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BetweenCriteria
 * @package Api\Repositories\Criterias\Core
 */
class BetweenCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $column;

    /**
     * BetweenCriteria constructor.
     * @param $column
     * @param $from
     * @param $to
     */
    public function __construct($column, $from, $to)
    {
        $this->to = $to;
        $this->from = $from;
        $this->column = $column;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->getTable($query);

        return $query->whereBetween("$table.$this->column", [$this->from, $this->to]);
    }
}
