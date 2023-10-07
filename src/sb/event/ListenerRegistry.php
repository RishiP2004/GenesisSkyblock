<?php

namespace sb\event;

use pocketmine\Server;
use sb\Skyblock;

final class ListenerRegistry {
	public static function register() : void {
		Server::getInstance()->getPluginManager()->registerEvents(new ItemListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new EntityListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new WorldListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new PlayerListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new BroadcastListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new IslandsListener(), Skyblock::getInstance());
		Server::getInstance()->getPluginManager()->registerEvents(new BlockListener(), Skyblock::getInstance());
	}
}