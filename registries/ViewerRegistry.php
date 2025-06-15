<?php

namespace UltraCitizens\registries;

use Closure;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;

class ViewerRegistry
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    /**
     * @var array<string, Player>
     */
    private array $viewers = [];

    /**
     * @return Player[]
     */
    public function getViewers(): array
    {
        return $this->viewers;
    }

    public function addViewer(Player $player): void
    {
        if (!isset($this->viewers[$player->getName()])) {
            $this->viewers[$player->getName()] = $player;
        }
    }

    public function removeViewer(Player $player): void
    {
        unset($this->viewers[$player->getName()]);
    }

    public function call(Closure $closure): void
    {
        Utils::validateCallableSignature(function (Player $player): void {}, $closure);

        foreach ($this->viewers as $viewer) {
            if ($viewer->isOnline()) {
                $closure($viewer);
            }
        }
    }
}