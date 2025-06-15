<?php

namespace UltraCitizens;

use UltraCitizens\listeners\CitizenListener;
use InvalidArgumentException;
use pocketmine\plugin\PluginBase;

class UltraCitizens
{
    private static PluginBase $plugin;

    private static bool $registered = false;

    public static function register(PluginBase $plugin): void
    {
        if (self::isRegistered()) {
            throw new InvalidArgumentException("{$plugin->getName()} attempted to register " . self::class . " twice.");
        }

        $plugin->getServer()->getPluginManager()->registerEvents(new CitizenListener(), $plugin);

        self::$plugin = $plugin;

        self::$registered = true;
    }

    public static function getPlugin(): PluginBase
    {
        return self::$plugin;
    }

    public static function isRegistered(): bool
    {
        return self::$registered;
    }
}