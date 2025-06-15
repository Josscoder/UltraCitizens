<?php

namespace Josscoder\UltraCitizens\impl\tag;

use Closure;
use Josscoder\UltraCitizens\UltraCitizens;
use pocketmine\scheduler\ClosureTask;

class UpdatableTag extends PlaceholderTag
{
    public function __construct(Closure $placeholder, int $ticks = 20, int $separator = 1)
    {
        parent::__construct($placeholder, $separator);

        UltraCitizens::getPlugin()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($placeholder): void {
            $this->rename($placeholder);
        }), $ticks);
    }
}