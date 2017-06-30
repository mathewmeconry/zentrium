<?php

namespace Zentrium\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleHierarchy implements RoleHierarchyInterface
{
    private $roles;
    private $flattened;

    public function __construct()
    {
        $this->roles = [];
    }

    public function register($name, $label, array $inheritedRoles = [])
    {
        $this->roles[$name] = [$label, $inheritedRoles];
        $this->flattened = null;
    }

    public function all()
    {
        return $this->roles;
    }

    public function getReachableRoles(array $roles)
    {
        $reachable = [];
        foreach ($roles as $role) {
            $reachable = array_merge($reachable, $this->flatten($role->getRole()));
        }

        $result = [];
        foreach (array_unique($reachable) as $role) {
            $result[] = new Role($role);
        }

        return $result;
    }

    private function flatten($role)
    {
        if (isset($this->flattened[$role])) {
            return $this->flattened[$role];
        }

        $result = [$role];
        if (isset($this->roles[$role])) {
            foreach ($this->roles[$role][1] as $inherited) {
                $result = array_merge($result, $this->flatten($inherited));
            }
        }

        return $this->flattened[$role] = $result;
    }
}
