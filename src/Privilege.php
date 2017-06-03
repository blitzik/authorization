<?php declare(strict_types=1);

namespace blitzik\Authorization;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="privilege")
 *
 */
class Privilege
{
    use Identifier;


    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';
    const VIEW = 'view';


    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @var string
     */
    private $name;


    public function __construct(string $name)
    {
        $this->setName($name);
    }


    private function setName(string $name): void
    {
        Validators::assert($name, 'unicode:1..255');
        $this->name = $name;
    }


    public function getName(): string
    {
        return $this->name;
    }

}