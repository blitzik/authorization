<?php declare(strict_types=1);

namespace blitzik\Authorization\Authorizator;

use blitzik\Authorization\Resource;
use Nette\Security\IAuthorizator;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidStateException;
use blitzik\Authorization\Role;
use Nette\Security\IIdentity;
use Nette\Security\Permission;
use Nette\Security\IResource;
use Nette\Caching\IStorage;
use Nette\Utils\Validators;
use Nette\Security\IRole;
use Nette\Caching\Cache;
use Nette\SmartObject;

class Authorizator implements IAuthorizator
{
    use SmartObject;


    const CACHE_NAMESPACE = 'blitzik.authorization';

    /** @var Cache */
    private $cache;

    /** @var Permission */
    private $acl;

    /** @var EntityManager */
    private $em;

    /** @var AuthorizationAssertionsCollection|null */
    private $assertionsCollection;


    public function __construct(
        AuthorizationAssertionsCollection $assertionsCollection = null,
        EntityManager $entityManager,
        IStorage $storage
    ) {
        $this->assertionsCollection = $assertionsCollection;
        $this->em = $entityManager;
        $this->cache = new Cache($storage, self::CACHE_NAMESPACE);
        $this->acl = $this->loadACL();
    }


    /**
     * @param \Nette\Security\User|IRole|Role|string $role
     * @param IResource|string $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege): bool
    {
        $roles = $this->resolveRoles($role);

        try {
            foreach ($roles as $_role) {
                if ($this->acl->isAllowed($_role, $resource, $privilege) === true) {
                    return true;
                }
            }

        } catch (InvalidStateException $e) {
            return false; // role does not exists
        }

        return false;
    }


    /**
     * @param \Nette\Security\User|IRole|Role|string $role
     * @return array
     */
    private function resolveRoles($role): array
    {
        $roles = [];
        if (Validators::is($role, 'unicode')) {
            $roles[] = $role;

        } elseif ($role instanceof Role) {
            $roles[] = $role->getName();

        } elseif ($role instanceof IRole) {
            $roles[] = $role;

        } elseif ($role instanceof IIdentity) {
            foreach ($role->getRoles() as $i_role) {
                $roles[] = $i_role;
            }

        } elseif ($role instanceof \Nette\Security\User) {
            $identity = $role->getIdentity(); // identity is User entity that implements IRole interface
            foreach ($identity->getRoles() as $i_role) {
                $roles[] = $i_role;
            }

        } else {
            throw new \InvalidArgumentException;
        }

        return $roles;
    }


    private function loadACL(): Permission
    {
        return $this->cache->load('acl', function () {
            return $this->createACL();
        });
    }


    private function createACL() : Permission
    {
        $acl = new Permission();

        $this->loadRoles($acl);
        $this->loadResources($acl);
        $this->loadPermissions($acl);

        return $acl;
    }


    private function loadRoles(Permission $acl): void
    {
        $roles = $this->em->createQuery(
            'SELECT r, parent FROM ' . Role::class . ' r
             LEFT JOIN r.parent parent
             ORDER BY r.parent ASC'
        )->execute();

        /** @var Role $role */
        foreach ($roles as $role) {
            $acl->addRole($role->getName(), $role->hasParent() ? $role->getParent()->getName() : null);
        }

        $acl->addRole(Role::GOD);
    }


    private function loadResources(Permission $acl): void
    {
        $resources = $this->em->createQuery(
            'SELECT r FROM ' . Resource::class . ' r'
        )->execute();

        /** @var Resource $resource */
        foreach ($resources as $resource) {
            $acl->addResource($resource->getName());
        }
    }


    private function loadPermissions(Permission $acl): void
    {
        $permissions = $this->em->createQuery(
            'SELECT p, pr FROM ' . \blitzik\Authorization\Permission::class . ' p
             LEFT JOIN p.privilege pr'
        )->execute();

        /** @var \blitzik\Authorization\Permission $permission */
        foreach ($permissions as $permission) {
            if ($permission->isAllowed() === true) {
                $assertion = $this->assertionsCollection->getAssertionForAllowed($permission->getResourceName(), $permission->getPrivilegeName());
                $acl->allow($permission->getRoleName(), $permission->getResourceName(), $permission->getPrivilegeName(), ($assertion !== null ? [$assertion, 'assert'] : null));
            } else {
                $assertion = $this->assertionsCollection->getAssertionForDenied($permission->getResourceName(), $permission->getPrivilegeName());
                $acl->deny($permission->getRoleName(), $permission->getResourceName(), $permission->getPrivilegeName(), ($assertion !== null ? [$assertion, 'assert'] : null));
            }
        }

        $acl->allow(Role::GOD, IAuthorizator::ALL, IAuthorizator::ALL);
    }

}