<?php

namespace UltraCitizens\impl\tag;

use UltraCitizens\config\Attributes;
use UltraCitizens\impl\Citizen;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

abstract class Tag extends Citizen
{
    public function __construct(private readonly int $separator = 1)
    {
        parent::__construct(Attributes::new()
            ->setNetworkId(EntityIds::CREEPER)
            ->setScale(0.001)
        );
    }

    public function getSeparator(): int
    {
        return $this->separator;
    }
}