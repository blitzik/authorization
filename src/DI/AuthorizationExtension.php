<?php declare(strict_types=1);

namespace blitzik\Authorization\DI;

use blitzik\Authorization\Authorizator\AuthorizationAssertionsCollection;
use blitzik\Authorization\Authorizator\IAuthorizationAssertion;
use blitzik\Authorization\Authorizator\Authorizator;
use Kdyby\Doctrine\DI\IEntityProvider;
use Nette\DI\CompilerExtension;

class AuthorizationExtension extends CompilerExtension implements IEntityProvider
{
    public function loadConfiguration(): void
    {
        $cb = $this->getContainerBuilder();

        $authorizator = $cb->addDefinition($this->prefix('authorizator'));
        $authorizator->setClass(Authorizator::class);

        $assertionCollection = $cb->addDefinition($this->prefix('authorizationAssertionsCollection'));
        $assertionCollection->setClass(AuthorizationAssertionsCollection::class);
    }


    public function beforeCompile(): void
    {
        $cb = $this->getContainerBuilder();

        $assertionCollection = $cb->getDefinition($this->prefix('authorizationAssertionsCollection'));
        $authorizator = $cb->getDefinition($this->prefix('authorizator'));

        foreach ($cb->findByType(IAuthorizationAssertion::class) as $assertion) {
            $assertionCollection->addSetup('addAssertion', ['assertion' => $assertion]);
        }
        $authorizator->setArguments(['assertionsCollection' => $assertionCollection]);
    }


    function getEntityMappings(): array
    {
        return ['blitzik\Authorization' => __DIR__ . '/..'];
    }

}