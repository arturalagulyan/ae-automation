<?php
namespace Api\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseApiModel
 * @package Api\Models
 */
abstract class BaseApiModel extends Model
{
    /**
     *
     */
    public static function boot()
    {
        parent::boot();
    }
}
