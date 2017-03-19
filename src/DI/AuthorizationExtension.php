<?php declare(strict_types=1);

namespace blitzik\Authorization\DI;

use blitzik\Authorization\Authorizator\AuthorizationAssertionsCollection;
use blitzik\Authorization\Authorizator\IAuthorizationAssertion;
use blitzik\Authorization\Authorizator\Authorizator;
use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;


class AuthorizationExtension extends CompilerExtension implements IEntityProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();

        $authorizator = $cb->addDefinition($this->prefix('authorizator'));
        $authorizator->setClass(Authorizator::class);

        $assertionCollection = $cb->addDefinition($this->prefix('authorizationAssertionsCollection'));
        $assertionCollection->setClass(AuthorizationAssertionsCollection::class);
        foreach ($cb->findByType(IAuthorizationAssertion::class) as $assertion) {
            $assertionCollection->addSetup('addAssertion', ['assertion' => $assertion]);
        }
        $authorizator->setArguments(['assertionsCollection' => $assertionCollection]);
    }


    function getEntityMappings()
    {
        return ['blitzik\Authorization' => __DIR__ . '/..'];
    }

}