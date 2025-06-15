<?php

namespace UltraCitizens\registries;

use UltraCitizens\impl\Citizen;
use UltraCitizens\impl\TagAbleCitizen;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class CitizenRegistry
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    /**
     * @var Citizen[]
     */
    private array $citizens = [];

    public function __construct()
    {
        self::setInstance($this);
    }

    /**
     * @return Citizen[]
     */
    public function getCitizens(): array
    {
        return $this->citizens;
    }

    /**
     * @param World $world
     * @return Citizen[]
     */
    public function getWorldCitizens(World $world): array
    {
        return array_filter($this->citizens, function (Citizen $citizen) use ($world) {
            return $citizen->getAttributes()?->getLocation()->getWorld() === $world;
        });
    }

    public function getCitizen(int $actorId): ?Citizen
    {
        return $this->citizens[$actorId] ?? null;
    }

    public function addCitizen(Citizen $citizen): void
    {
        if (isset($this->citizens[$citizen->getActorId()])) {
            return;
        }

        $this->citizens[$citizen->getActorId()] = $citizen;
        $citizen->showToAll();

        if (!($citizen instanceof TagAbleCitizen)) {
            return;
        }

        foreach ($citizen->getTagEditor()->getTags() as $tag) {
            $this->citizens[$tag->getActorId()] = $tag;
        }
    }

    public function removeCitizen(Citizen $citizen): void
    {
        unset($this->citizens[$citizen->getActorId()]);
        $citizen->hideFromAll();

        if (!($citizen instanceof TagAbleCitizen)) {
            return;
        }

        foreach ($citizen->getTagEditor()->getTags() as $tag) {
            unset($this->citizens[$tag->getActorId()]);
        }
    }
}