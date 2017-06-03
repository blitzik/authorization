<?php declare(strict_types=1);

namespace blitzik\Authorization\Authorizator;

use Nette\Security\Permission;

/**
 * Class that implements this interface just adds
 * specific Roles, Resources and privileges to ACL
 * generated from database
 *
 * @package Users\Authorization
 */
interface IAuthorizationAssertion
{
    /**
     * Is this definition meant to be used in allowed privilege?
     *
     * @return bool
     */
    public function isForAllowed(): bool;


    /**
     * @return string
     */
    public function getResourceName(): string;


    /**
     * @return array
     */
    public function getPrivilegeNames(): array;


    /**
     * @param Permission $acl
     * @param $role
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function assert(Permission $acl, $role, $resource, $privilege): bool;
}