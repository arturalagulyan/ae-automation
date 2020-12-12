<?php

namespace Api\Transformers;

use Api\Models\User;

/**
 * Class AuthUserTransformer
 * @package Api\Transformers
 */
class AuthUserTransformer extends BaseApiTransformer
{
    /**
     * @param User $user
     * @return mixed
     * @throws \Exception
     */
    public function transform($user)
    {
        $transformed = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        $transformed = array_merge($transformed, $this->transformPermissions($user));

        return $transformed;
    }

    /**
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function transformPermissions($user)
    {
        $roleConstants = get_class_constants(\ConstRoles::class);
        $permissionConstants = get_class_constants(\ConstPermissions::class);

        $roles = [];

        foreach ($roleConstants as $constant => $role) {
            if ($user->hasRole($role)) {
                $roles[$constant] = true;
            } else {
                $roles[$constant] = false;
            }
        }

        $permissions = [];

        foreach ($permissionConstants as $constant => $permission) {
            if ($user->can($permission)) {
                $permissions[$constant] = true;
            } else {
                $permissions[$constant] = false;
            }
        }

        return [
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }
}
