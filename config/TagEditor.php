<?php

namespace UltraCitizens\config;

use UltraCitizens\impl\tag\Tag;
use UltraCitizens\impl\Citizen;
use pocketmine\entity\Location;

class TagEditor
{
    const ONE_BREAK_LINE = 0.32;

    private float $height = 1.8;

    /**
     * @var Tag[]
     */
    private array $tags = [];

    public static function new(): self
    {
        return new self();
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function putTag(Tag $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function getTag(int $index): ?Tag
    {
        return array_values($this->tags)[max(count($this->tags) - $index -1, 0)] ?? null;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function link(Citizen $citizen): void
    {
        $this->tags = array_reverse($this->tags);

        $attributes = $citizen->getAttributes();

        foreach ($this->tags as $index => $tag) {
            $newLocation = null;

            if ($index === 0) {
                $newLocation = $citizen->getAttributes()?->getLocation()->add(0, $this->height, 0);
            } else {
                $previousLine = $this->tags[$index - 1];
                $newLocation = $previousLine->getAttributes()?->getLocation()->add(0, (self::ONE_BREAK_LINE * $tag->getSeparator()), 0);
            }

            $tag->getAttributes()->setLocation(Location::fromObject($newLocation, $attributes->getLocation()->getWorld()));

            if (!is_null($onClick = $attributes->getOnClick())) {
                $tag->getAttributes()->setOnClick($onClick);
            }
        }
    }
}