<?php declare(strict_types=1);

namespace blitzik\Authorization;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="access_definition",
 *     uniqueConstraints={@UniqueConstraint(name="resource_privilege", columns={"resource", "privilege"})}
 * )
 */
class AccessDefinition
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="Resource", cascade={"persist"})
     * @ORM\JoinColumn(name="resource", referencedColumnName="id", nullable=false)
     * @var Resource
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Privilege", cascade={"persist"})
     * @ORM\JoinColumn(name="privilege", referencedColumnName="id", nullable=false)
     * @var Privilege
     */
    private $privilege;


    public function __construct(
        \blitzik\Authorization\Resource $resource,
        Privilege $privilege
    ) {
        $this->resource = $resource;
        $this->privilege = $privilege;
    }


    public function getResourceId(): int
    {
        return $this->resource->getId();
    }


    public function getResourceName(): string
    {
        return $this->resource->getName();
    }


    public function getPrivilegeId(): int
    {
        return $this->privilege->getId();
    }


    public function getPrivilegeName(): string
    {
        return $this->privilege->getName();
    }
}