<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereInCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereInCriteria extends BaseApiCriteria
{
    /**
     * @var array
     */
    private $values;

    /**
     * @var string
     */
    private $attribute;

    /**
     * WhereInCriteria constructor.
     * @param $attribute
     * @param array $values
     */
    public function __construct($attribute, $values = [])
    {
        if (!is_array($values)) {
            $values = [
                $values
            ];
        }
        $this->attribute = $attribute;
        $this->values = $values;
    }
    /**
     * @param Builder $query
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($query, RepositoryInterface $repository)
    {
        $table = $this->getTable($query);

        return $query->whereIn("$table.$this->attribute", $this->values);
    }
}
