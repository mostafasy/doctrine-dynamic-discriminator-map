<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Employee extends Person
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $department = null;

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): void
    {
        $this->department = $department;
    }
}
