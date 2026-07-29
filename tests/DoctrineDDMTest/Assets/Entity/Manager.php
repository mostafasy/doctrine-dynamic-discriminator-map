<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Manager extends Person
{
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $teamSize = null;

    public function getTeamSize(): ?int
    {
        return $this->teamSize;
    }

    public function setTeamSize(?int $teamSize): void
    {
        $this->teamSize = $teamSize;
    }
}
