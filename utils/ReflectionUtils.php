<?php

namespace UltraCitizens\utils;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use ReflectionClass;

//Taken from: https://github.com/CustomiesDevs/Customies/blob/master/src/entity/CustomiesEntityFactory.php
class ReflectionUtils
{
    public static function updateStaticPacketCache(string $identifier, string $behaviourId): void {
        $instance = StaticPacketCache::getInstance();
        $property = (new ReflectionClass($instance))->getProperty("availableActorIdentifiers");
        /** @var AvailableActorIdentifiersPacket $packet */
        $packet = $property->getValue($instance);
        /** @var CompoundTag $root */
        $root = $packet->identifiers->getRoot();
        ($root->getListTag("idlist") ?? new ListTag())->push(CompoundTag::create()
            ->setString("id", $identifier)
            ->setString("bid", $behaviourId));
        $packet->identifiers = new CacheableNbt($root);
    }
}