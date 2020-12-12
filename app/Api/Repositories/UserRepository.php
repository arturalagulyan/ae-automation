<?php

namespace Api\Repositories;

use Api\Models\User;

/**
 * Class UserRepository
 * @package Api\Repositories
 */
class UserRepository extends BaseApiRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param int $id
     * @param array $roles
     * @return mixed
     */
    public function assignRoles($id, array $roles)
    {
        return $this->find($id)->assignRole($roles);
    }

    /**
     * @param int $id
     * @param array $roles
     * @return mixed
     */
    public function syncRoles($id, $roles)
    {
        return $this->find($id)->syncRoles($roles);
    }
}
