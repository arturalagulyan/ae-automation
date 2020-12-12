<?php

namespace Api\Repositories\Criterias\Core;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class WhereIntegerInRawCriteria
 * @package Api\Repositories\Criterias\Core
 */
class WhereIntegerInRawCriteria extends BaseApiCriteria
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
     * WhereIntegerInRawCriteria constructor.
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

        return $query->whereIntegerInRaw("$table.$this->attribute", $this->values);
    }
}
