<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class HavingRawCriteria
 * @package Api\Repositories\Criterias\Core
 */
class HavingRawCriteria extends BaseApiCriteria
{
    /**
     * @var
     */
    protected $value;

    /**
     * HavingRawCriteria constructor.
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
        return $query->havingRaw($this->value);
    }
}
