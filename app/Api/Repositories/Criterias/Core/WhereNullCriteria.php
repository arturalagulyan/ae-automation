<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereNullCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereNullCriteria extends BaseApiCriteria
{
    /**
     * @var string
     */
    private $attribute;

    /**
     * WhereNullCriteria constructor.
     * @param $attribute
     */
    public function __construct($attribute)
    {
        $this->attribute = $attribute;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->getTable($query);

        return $query->whereNull("$table.$this->attribute");
    }
}
