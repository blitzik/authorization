<?php declare(strict_types=1);

namespace blitzik\Authorization\Authorizator;


interface IResource extends \Nette\Security\IResource
{
    /**
     * @return mixed
     */
    public function getOwnerId();
}