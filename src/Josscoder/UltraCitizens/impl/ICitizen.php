<?php

namespace Josscoder\UltraCitizens\impl;

use pocketmine\player\Player;

interface ICitizen
{
    public function showTo(Player $player): void;

    public function showToAll(): void;

    public function hideFrom(Player $player): void;

    public function hideFromAll(): void;
}