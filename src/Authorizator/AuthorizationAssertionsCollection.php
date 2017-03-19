<?php declare(strict_types=1);

namespace blitzik\Authorization\Authorizator;

use Nette\SmartObject;


class AuthorizationAssertionsCollection
{
    use SmartObject;
    
    
    /** @var IAuthorizationAssertion[] */
    private $definitions = [];


    public function addAssertion(IAuthorizationAssertion $assertion)
    {
        foreach ($assertion->getPrivilegeNames() as $privilegeName) {
            $this->definitions[$assertion->getResourceName()]
                              [(bool)$assertion->isForAllowed()]
                              [$privilegeName] = $assertion;
        }
    }


    /**
     * @param $resource
     * @param $privilege
     * @return IAuthorizationAssertion|null
     */
    public function getAssertionForAllowed($resource, $privilege)
    {
        if (isset($this->definitions[$resource][true][$privilege])) {
            return $this->definitions[$resource][true][$privilege];
        }

        return null;
    }


    /**
     * @param $resource
     * @param $privilege
     * @return IAuthorizationAssertion|null
     */
    public function getAssertionForDenied($resource, $privilege)
    {
        if (isset($this->definitions[$resource][false][$privilege])) {
            return $this->definitions[$resource][false][$privilege];
        }

        return null;
    }
}