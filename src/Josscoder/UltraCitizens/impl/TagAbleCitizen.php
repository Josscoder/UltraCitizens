<?php

namespace Josscoder\UltraCitizens\impl;

use Josscoder\UltraCitizens\config\Attributes;
use Josscoder\UltraCitizens\config\TagEditor;
use pocketmine\player\Player;

class TagAbleCitizen extends Citizen
{
    public function __construct(Attributes $attributes, private readonly TagEditor $tagEditor)
    {
        parent::__construct($attributes);

        $tagEditor->link($this);
    }

    public function getTagEditor(): TagEditor
    {
        return $this->tagEditor;
    }

    public function showLinesTo(Player $player): void
    {
        foreach ($this->tagEditor->getTags() as $tag) {
            $tag->showTo($player);
        }
    }

    public function showTo(Player $player): void
    {
        parent::showTo($player);
        $this->showLinesTo($player);
    }

    public function hideLinesFrom(Player $player): void
    {
        foreach ($this->tagEditor->getTags() as $tag) {
            $tag->hideFrom($player);
        }
    }

    public function hideFrom(Player $player): void
    {
        parent::hideFrom($player);
        $this->hideLinesFrom($player);
    }
}