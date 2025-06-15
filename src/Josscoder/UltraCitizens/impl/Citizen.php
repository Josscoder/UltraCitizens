<?php

namespace Josscoder\UltraCitizens\impl;

use Josscoder\UltraCitizens\config\Attributes;
use Josscoder\UltraCitizens\registries\ViewerRegistry;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;

class Citizen implements ICitizen
{
    private int $actorId;

    private ViewerRegistry $viewerRegistry;

    public function __construct(
        private readonly Attributes $attributes
    ) {
        $this->actorId = Entity::nextRuntimeId();

        $this->viewerRegistry = new ViewerRegistry();
    }

    public function getActorId(): int
    {
        return $this->actorId;
    }

    public function getViewerRegistry(): ViewerRegistry
    {
        return $this->viewerRegistry;
    }

    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @return MetadataProperty[]
     * @phpstan-return array<int, MetadataProperty>
     */
    protected function getInitialMetadata(): array
    {
        /** @var array<int, MetadataProperty> $metadata */
        $metadata = [
            EntityMetadataProperties::NAMETAG => new StringMetadataProperty(''),
            EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
            EntityMetadataProperties::LEAD_HOLDER_EID => new IntMetadataProperty(-1),
            EntityMetadataProperties::SCALE => new FloatMetadataProperty($this->attributes->getScale())
        ];

        $boundingBoxHeight = $this->attributes->getBoundingBoxHeight();
        if (!is_null($boundingBoxHeight)) {
            $metadata[EntityMetadataProperties::BOUNDING_BOX_HEIGHT] = new FloatMetadataProperty($boundingBoxHeight);
        }

        $boundingBoxWidth = $this->attributes->getBoundingBoxWidth();
        if (!is_null($boundingBoxWidth)) {
            $metadata[EntityMetadataProperties::BOUNDING_BOX_WIDTH] = new FloatMetadataProperty($boundingBoxWidth);
        }

        $variant = $this->attributes->getVariant();
        if ($variant != -1) {
            $metadata[EntityMetadataProperties::VARIANT] = new IntMetadataProperty($variant);
        }

        return $metadata;
    }

    public function showTo(Player $player): void
    {
        $location = $this->attributes?->getLocation();
        if (!$location->isValid()) {
            return;
        }

        /**
         * @param MetadataProperty[] $metadata
         * @phpstan-param array<int, MetadataProperty> $metadata
         */
        $metadata = $this->getInitialMetadata();

        $packet = AddActorPacket::create(
            $this->actorId,
            $this->actorId,
            $this->attributes->getNetworkId(),
            $location->asVector3(),
            null,
            $location->getPitch(),
            $location->getYaw(),
            $location->getYaw(),
            0,
            [],
            $metadata,
            new PropertySyncData([], []),
            []
        );

        $player->getNetworkSession()->sendDataPacket($packet);

        $this->viewerRegistry->addViewer($player);
    }

    public function showToAll(): void
    {
        $location = $this->getAttributes()?->getLocation();
        if (!$location->isValid()) {
            return;
        }

        foreach ($location->getWorld()->getPlayers() as $player) {
            $this->showTo($player);
        }
    }

    public function hideFrom(Player $player): void
    {
        $player->getNetworkSession()->sendDataPacket(RemoveActorPacket::create($this->actorId));

        $this->viewerRegistry->removeViewer($player);
    }

    public function hideFromAll(): void
    {
        $location = $this->getAttributes()?->getLocation();
        if (!$location->isValid()) {
            return;
        }

        foreach ($location->getWorld()->getPlayers() as $player) {
            $this->hideFrom($player);
        }
    }

    /**
     * @param MetadataProperty[] $metadata
     * @phpstan-param array<int, MetadataProperty> $metadata
     * @return void
     */
    public function updateMetadata(array $metadata): void
    {
        $this->viewerRegistry->call(function (Player $player) use ($metadata): void {
            $this->updateMetadataForPlayer($metadata, $player);
        });
    }

    /**
     * @param MetadataProperty[] $metadata
     * @param Player $player
     * @phpstan-param array<int, MetadataProperty> $metadata
     * @return void
     */
    public function updateMetadataForPlayer(array $metadata, Player $player): void
    {
        $player->getNetworkSession()->sendDataPacket(SetActorDataPacket::create(
            $this->actorId,
            $metadata,
            new PropertySyncData([], []),
            0
        ));
    }
}