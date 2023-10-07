<?php
namespace sb\lang;

interface CustomKnownTranslationKeys{
	const BANK_NOTE_REDEEM = "&r&a&l+ $&r&a%money";
	const MONEY_POUCH_REDEEM = "&r&a&l(!) &r&aYou have redeemed &r&a$%money &r&afrom a money pouch.";
	const XP_BOTTLE_SUCCESS = "&r&a&l+ %xp% xp";
	const PLAYER_ALREADY_TELEPORTING = "&r&c&l(!) &r&cYou already have a pending teleport request!";
	const PLAYER_HAS_NO_PERMISSION_KIT = "&r&4&l(!) &r&4You are missing the required permissions for the kit %kit.";
	const PLAYER_HAS_KIT_COOLDOWN = "&r&c&l(!) &r&cThe kit '%kit%' is currently on cooldown.";
	const PLAYER_RECEIVED_KIT =  "&r&6You have successfully claimed kit &r&c%kit%&6.";
	const ISLAND_CREATE = "&r&3&lWelcome to your own island!\n&r&7This is your own personal island. You can create the island of\n&r&7your dreams! You can invite your friends and play together.\n&r&8- &r&7Use &r&e/island help &r&7for extra information.\n&r&8- &r&7Use &r&e/island go &r&7to teleport back to your island.";
	const PERK_GRANT_PERMISSION =  "&r&b&l(!) &r&bHooray! You have &b&lUNLOCKED &r&bthe &r&d&l%command% &r&bcommand permanently!";
    const ISLAND_TELEPORT =  "&r&a&l(!) &r&aYou have been teleported to your island.\n&r&7For more information, use &r&7/island help.";
	const ISLAND_DISBAND_SUCCESS = "&r&c&l(!) &r&cYou island was deleted successfully.\n&r&7You can create a new island with &r&7/island create.";
}