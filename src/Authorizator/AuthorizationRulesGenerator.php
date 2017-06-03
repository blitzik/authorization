<?php declare(strict_types=1);

namespace blitzik\Authorization\Authorizator;

use Doctrine\Common\DataFixtures\AbstractFixture;
use blitzik\Authorization\AccessDefinition;
use blitzik\Authorization\Permission;
use blitzik\Authorization\Privilege;
use blitzik\Authorization\Resource;
use Kdyby\Doctrine\EntityManager;
use blitzik\Authorization\Role;
use Nette\SmartObject;

class AuthorizationRulesGenerator
{
    use SmartObject;

    /** @var AbstractFixture */
    private $fixture;
    
    /** @var EntityManager */
    private $em;

    /** @var \Users\Authorization\Resource */
    private $resource;

    
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    public function setFixture(AbstractFixture $fixture): void
    {
        $this->fixture = $fixture;
    }


    public function addResource(Resource $resource, string $fixtureObjectReferenceName = null): AuthorizationRulesGenerator
    {
        $this->em->persist($resource);
        $this->resource = $resource;

        if ($fixtureObjectReferenceName !== null and $this->fixture !== null) {
            $this->fixture->addReference($fixtureObjectReferenceName, $resource);
        }

        return $this;
    }


    public function updateResource(Resource $resource): AuthorizationRulesGenerator
    {
       $this->resource = $resource;
        return $this;
    }


    public function addDefinition(Privilege $privilege, Role $role): AuthorizationRulesGenerator
    {
        $accessDefinition = new AccessDefinition($this->resource, $privilege);
        $this->em->persist($accessDefinition);

        $permission = new Permission($role, $this->resource, $privilege);
        $this->em->persist($permission);

        return $this;
    }
}