<?php
namespace Api\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role
 * @package App\Models\V3\User
 */
class Role extends SpatieRole
{

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

}
