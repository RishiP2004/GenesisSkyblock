<?php

declare(strict_types = 1);

namespace sb\server\broadcast;

use sb\event\BroadcastListener;
use sb\Skyblock;

use sb\scheduler\server\broadcast\BroadcastSendTask;
use pocketmine\Server;

class BroadcastHandler implements Broadcasts {
	const TITLE = "TITLE";
	const POPUP = "POPUP";

    private static int $runs = 0;
	private static int $length = -1;

    public static function tick() : void {
        self::$runs++;

        if(self::AUTOS["message"]) {
            if(self::$runs === self::TIMES["message"] * 20) {
				self::$length++;
				self::$runs = self::$length + 1;
                $messages = self::MESSAGES;
                $messageKey = self::$length;
                $message = $messages[$messageKey];

                if(self::$length === count($messages) - 1) {
					self::$length = -1;
                }
                Server::getInstance()->broadcastMessage(self::send($message));
            }
        }
        if(self::AUTOS["popup"]) {
			if(self::$runs === self::TIMES["popup"] * 20) {
				self::$length++;
				self::$runs = self::$length + 1;
                $popups = self::POPUPS;
                $popupKey = self::$length;
                $popup = $popups[$popupKey];

				if(self::$length === count($popups) - 1) {
					self::$length = -1;
				}
                Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new BroadcastSendTask(self::POPUP, null, self::DURATIONS["popup"], self::send($popup)), 10);
            }
        }
        if(self::AUTOS["title"]) {
            if(self::$runs === self::TIMES["title"] * 20) {
				self::$length++;
				self::$runs = self::$length + 1;
                $titles = self::TITLES;
                $titleKey = self::$length;
                $title = $titles[$titleKey];

                if(self::$length === count($titles) - 1) {
                    self::$length = -1;
                }
                $subTitle = str_replace(array_shift($title), ":", "");

				Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new BroadcastSendTask(self::TITLE, null, self::DURATIONS["title"], self::send($title), self::send($subTitle)), 10);
            }
        }
    }

    public static function send(string $broadcast) : string {
		return str_replace([
			"{PREFIX}",
			"{TIME}",
			"{MAX_PLAYERS}",
			"{TOTAL_PLAYERS}"
		], [
			Skyblock::PREFIX,
			date(self::FORMATS["date_time"]),
			Server::getInstance()->getMaxPlayers(),
			count(Server::getInstance()->getOnlinePlayers())
		], $broadcast);
    }
}