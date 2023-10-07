<?php

namespace sb\lang;
use pocketmine\utils\TextFormat as TF;
use pocketmine\lang\Language;
use pocketmine\lang\Translatable;

class CustomKnownTranslationFactory{
	public static function bank_note_redeem(string $amount): Translatable{
		return self::replace(CustomKnownTranslationKeys::BANK_NOTE_REDEEM, [
			"money" => $amount
		]);
	}
	public static function money_pouch_redeem(string $number_format): Translatable{
		return self::replace(CustomKnownTranslationKeys::MONEY_POUCH_REDEEM, [
			"money" => $number_format
		]);
	}

	public static function xp_bottle_success(string $amount): Translatable{
		return self::replace(CustomKnownTranslationKeys::XP_BOTTLE_SUCCESS, [
			"xp" => $amount
		]);
	}

	public static function player_already_teleporting(): Translatable{
		return self::replace(CustomKnownTranslationKeys::PLAYER_ALREADY_TELEPORTING);
	}

	public static function player_has_no_permission_kit(string $kitname): Translatable{
		return self::replace(CustomKnownTranslationKeys::PLAYER_HAS_NO_PERMISSION_KIT, [
			"kit" => $kitname
		]);
	}

	public static function player_has_kit_cooldown(string $kitname): Translatable{
		return self::replace(CustomKnownTranslationKeys::PLAYER_HAS_KIT_COOLDOWN, [
			"kit" => $kitname
		]);
	}

	public static function player_received_kit(mixed $kitName): Translatable{
		return self::replace(CustomKnownTranslationKeys::PLAYER_RECEIVED_KIT, [
			"kit" => $kitName
		]);
	}

	public static function perk_grant_permission(string $perm): Translatable{
		return self::replace(CustomKnownTranslationKeys::PERK_GRANT_PERMISSION, [
			"command" => $perm
		]);
	}

	public static function island_created(): Translatable{
		return self::replace(CustomKnownTranslationKeys::ISLAND_CREATE);
	}

	public static function island_teleported(): Translatable{
		return self::replace(CustomKnownTranslationKeys::ISLAND_TELEPORT);
	}

	public static function replace(string $message, array $params = []):  Translatable{
		foreach ($params as $key => $value){
			$message = str_replace("%$key", (string)$value, $message);
		}

		return new Translatable(TF::colorize($message), $params);
	}

	public static function island_disband_success(): Translatable{
		return self::replace(CustomKnownTranslationKeys::ISLAND_DISBAND_SUCCESS);
	}
}
