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


    /**
     * @param AbstractFixture $fixture
     */
    public function setFixture(AbstractFixture $fixture)
    {
        $this->fixture = $fixture;
    }


    /**
     * @param Resource $resource
     * @param string|null $fixtureObjectReferenceName
     * @return AuthorizationRulesGenerator
     */
    public function addResource(Resource $resource, $fixtureObjectReferenceName = null): AuthorizationRulesGenerator
    {
        $this->em->persist($resource);
        $this->resource = $resource;

        if ($fixtureObjectReferenceName !== null and $this->fixture !== null) {
            $this->fixture->addReference($fixtureObjectReferenceName, $resource);
        }

        return $this;
    }


    /**
     * @param Resource $resource
     * @return AuthorizationRulesGenerator
     */
    public function updateResource(Resource $resource): AuthorizationRulesGenerator
    {
       $this->resource = $resource;
        return $this;
    }


    /**
     * @param Privilege $privilege
     * @param Role $role
     * @return AuthorizationRulesGenerator
     */
    public function addDefinition(Privilege $privilege, Role $role): AuthorizationRulesGenerator
    {
        $accessDefinition = new AccessDefinition($this->resource, $privilege);
        $this->em->persist($accessDefinition);

        $permission = new Permission($role, $this->resource, $privilege);
        $this->em->persist($permission);

        return $this;
    }
}