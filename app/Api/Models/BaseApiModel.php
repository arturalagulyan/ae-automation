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

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }
    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
