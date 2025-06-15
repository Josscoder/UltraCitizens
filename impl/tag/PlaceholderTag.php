<?php

namespace UltraCitizens\impl\tag;

use Closure;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

class PlaceholderTag extends Tag
{
    public function __construct(private Closure $placeholder, int $separator = 1)
    {
        parent::__construct($separator);

        Utils::validateCallableSignature(function (Player $player): string {
            return '';
        }, $placeholder);
    }

    public function rename(Closure $placeholder): void
    {
        $this->placeholder = $placeholder;

        $this->getViewerRegistry()->call(function (Player $player): void {
            $this->render($player);
        });
    }

    public function render(Player $player): void
    {
        $placeholder = $this->placeholder;
        $output = $placeholder($player);

        $metadata = [
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(TextFormat::colorize($output))
        ];

        $this->updateMetadataForPlayer($metadata, $player);
    }

    public function showTo(Player $player): void
    {
        parent::showTo($player);
        $this->render($player);
    }
}