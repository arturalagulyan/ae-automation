<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereRawCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereRawCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $value;

    /**
     * WhereRawCriteria constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        return $query->whereRaw($this->value);
    }
}
