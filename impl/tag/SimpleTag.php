<?php

namespace UltraCitizens\impl\tag;

use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\utils\TextFormat;

class SimpleTag extends Tag
{
    public function __construct(private string $text, int $separator = 1)
    {
        parent::__construct($separator);
    }

    public function rename(string $text): void
    {
        $this->text = $text;

        $metadata = [
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($this->text))
        ];

        $this->updateMetadata($metadata);
    }

    protected function getInitialMetadata(): array
    {
        $metadata = parent::getInitialMetadata();

        $metadata[EntityMetadataProperties::NAMETAG] = new StringMetadataProperty(TextFormat::colorize($this->text));

        return $metadata;
    }

    public function getText(): string
    {
        return $this->text;
    }
}