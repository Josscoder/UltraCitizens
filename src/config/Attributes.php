<?php

namespace UltraCitizens\config;

use Closure;
use UltraCitizens\utils\ReflectionUtils;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class Attributes
{
    private string $networkId = EntityIds::CREEPER;

    private bool $customEntity = false;

    private int $variant = -1;

    private ?Location $location = null;

    private float $scale = 1.0;

    private ?Closure $onClick = null;

    private ?float $boundingBoxHeight = null;

    private ?float $boundingBoxWidth = null;

    public static function new(): self
    {
        return new self();
    }

    public function getNetworkId(): string
    {
        return $this->networkId;
    }

    public function setNetworkId(string $networkId): self
    {
        $this->networkId = $networkId;

        return $this;
    }

    public function isCustomEntity(): bool
    {
        return $this->customEntity;
    }

    public function setCustomEntity(bool $customEntity = true): self
    {
        $this->customEntity = $customEntity;

        ReflectionUtils::updateStaticPacketCache($this->networkId, $this->networkId);

        return $this;
    }

    public function getVariant(): int
    {
        return $this->variant;
    }

    public function setVariant(int $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getScale(): float
    {
        return $this->scale;
    }

    public function setScale(float $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    public function getOnClick(): ?Closure
    {
        return $this->onClick;
    }

    public function setOnClick(?Closure $onClick): self
    {
        Utils::validateCallableSignature(function (Player $player): void {}, $onClick);

        $this->onClick = $onClick;

        return $this;
    }

    public function getBoundingBoxHeight(): ?float
    {
        return $this->boundingBoxHeight;
    }

    public function setBoundingBoxHeight(?float $boundingBoxHeight): self
    {
        $this->boundingBoxHeight = $boundingBoxHeight;

        return $this;
    }

    public function getBoundingBoxWidth(): ?float
    {
        return $this->boundingBoxWidth;
    }

    public function setBoundingBoxWidth(?float $boundingBoxWidth): self
    {
        $this->boundingBoxWidth = $boundingBoxWidth;

        return $this;
    }
}