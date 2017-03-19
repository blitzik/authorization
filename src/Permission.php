<?php declare(strict_types=1);

namespace blitzik\Authorization;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity
 * @ORM\Table(name="permission")
 *
 */
class Permission
{
    use Identifier;

    
    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var Role
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="resource", referencedColumnName="id", nullable=false)
     * @var Resource
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Privilege")
     * @ORM\JoinColumn(name="privilege", referencedColumnName="id", nullable=false)
     * @var Privilege
     */
    private $privilege;

    /**
     * @ORM\Column(name="is_allowed", type="boolean", nullable=false, unique=false, options={"default":true})
     * @var bool
     */
    private $isAllowed;


    public function __construct(
        Role $role,
        \blitzik\Authorization\Resource $resource,
        Privilege $privilege,
        bool $isAllowed = true
    ) {
        $this->role = $role;
        $this->resource = $resource;
        $this->privilege = $privilege;
        $this->isAllowed = $isAllowed;
    }


    public function isAllowed(): bool
    {
        return $this->isAllowed;
    }


    /*
     * ------------------------
     * ----- ROLE GETTERS -----
     * ------------------------
     */


    public function getRoleName(): string
    {
        return $this->role->getName();
    }
    

    /*
     * ----------------------------
     * ----- RESOURCE GETTERS -----
     * ----------------------------
     */


    public function getResourceId(): int
    {
        return $this->resource->getId();
    }


    public function getResourceName(): string
    {
        return $this->resource->getName();
    }


    /*
     * -----------------------------
     * ----- PRIVILEGE GETTERS -----
     * -----------------------------
     */


    public function getPrivilegeId(): int
    {
        return $this->privilege->getId();
    }


    public function getPrivilegeName(): string
    {
        return $this->privilege->getName();
    }

}