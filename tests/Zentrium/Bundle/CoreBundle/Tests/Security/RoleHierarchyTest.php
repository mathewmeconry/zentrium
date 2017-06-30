<?php

namespace Zentrium\Bundle\CoreBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\Role;
use Zentrium\Bundle\CoreBundle\Security\RoleHierarchy;

class RoleHierarchyTest extends TestCase
{
    public function testGetReachableRoles()
    {
        $hierarchy = new RoleHierarchy();
        $hierarchy->register('a', '', []);
        $hierarchy->register('b', '', ['a']);
        $hierarchy->register('c', '', ['b']);
        $hierarchy->register('d', '', ['c', 'e']);
        $hierarchy->register('f', '', ['g']);

        $result = $hierarchy->getReachableRoles([new Role('d'), new Role('f')]);

        $roles = array_map(function ($role) {
            return $role->getRole();
        }, $result);
        sort($roles);
        $this->assertSame($roles, ['a', 'b', 'c', 'd', 'e', 'f', 'g']);
    }

    public function testGetReachableRolesUnknown()
    {
        $hierarchy = new RoleHierarchy();

        $result = $hierarchy->getReachableRoles([new Role('x')]);

        $this->assertCount(1, $result);
        $this->assertSame('x', $result[0]->getRole());
    }
}
