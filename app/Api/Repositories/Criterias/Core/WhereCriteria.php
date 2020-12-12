<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $comparison;

    /**
     * WhereCriteria constructor.
     * @param $attribute
     * @param $value
     * @param string $comparison
     */
    public function __construct($attribute, $value, $comparison = '=')
    {
        $this->value = $value;
        $this->attribute = $attribute;
        $this->comparison = $comparison;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->getTable($query);

        return $query->where("$table.$this->attribute", $this->comparison, $this->value);
    }
}
