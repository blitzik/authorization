<?php declare(strict_types=1);

namespace blitzik\Authorization;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Nette\Security\IRole;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements IRole
{
    use Identifier;


    const MEMBER = 'member';
    const ADMIN = 'admin';
    const GOD = 'god';


    const LENGTH_NAME = 255;


    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Role", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, unique=false, onDelete="SET NULL")
     */
    private $parent;


    public function __construct(
        string $name,
        Role $parent = null
    ) {
        $this->setName($name);
        $this->parent = $parent;
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


    public function hasParent(): bool
    {
        return $this->parent !== null;
    }


    /*
     * --------------------------
     * ----- PARENT GETTERS -----
     * --------------------------
     */


    public function getParent(): ?Role
    {
        return $this->parent;
    }


    // -----


    function getRoleId()
    {
        return $this->getName();
    }


}