<?php

namespace Josscoder\UltraCitizens\listeners;

use Josscoder\UltraCitizens\UltraCitizens;
use Josscoder\UltraCitizens\registries\CitizenRegistry;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class CitizenListener implements Listener
{
    /**
     * @param PlayerJoinEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        foreach (CitizenRegistry::getInstance()->getWorldCitizens($player->getWorld()) as $citizen) {
            $citizen->showTo($player);
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        foreach (CitizenRegistry::getInstance()->getWorldCitizens($player->getWorld()) as $citizen) {
            $citizen->hideFrom($player);
        }
    }

    /**
     * @param EntityTeleportEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onWorldChange(EntityTeleportEvent $event): void
    {
        $entity = $event->getEntity();
        if (!($entity instanceof Player)) {
            return;
        }

        $fromWorld = $event->getFrom()->getWorld();
        $toWorld = $event->getTo()->getWorld();

        if ($fromWorld === $toWorld) {
            return;
        }

        UltraCitizens::getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity, $fromWorld, $toWorld): void {
            if (!$entity->isOnline()) {
                return;
            }

            if ($fromWorld->isLoaded()) {
                foreach (CitizenRegistry::getInstance()->getWorldCitizens($fromWorld) as $citizen) {
                    $citizen->hideFrom($entity);
                }
            }

            if ($toWorld->isLoaded()) {
                foreach (CitizenRegistry::getInstance()->getWorldCitizens($toWorld) as $citizen) {
                    $citizen->showTo($entity);
                }
            }
        }), 5);
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @priority HIGHEST
     * @return void
     */
    public function onClickCitizen(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if (!($packet instanceof InventoryTransactionPacket)) {
            return;
        }

        if ($packet->requestId != InventoryTransactionPacket::TYPE_NORMAL) {
            return;
        }

        $data = $packet->trData;
        if (!($data instanceof UseItemOnEntityTransactionData)) {
            return;
        }

        if (is_null($citizen = CitizenRegistry::getInstance()->getCitizen($data->getActorRuntimeId())) ||
            is_null($onClick = $citizen->getAttributes()->getOnClick())
        ) {
            return;
        }

        $onClick($event->getOrigin()->getPlayer());
    }
}