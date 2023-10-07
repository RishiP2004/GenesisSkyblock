<?php

declare(strict_types = 1);

namespace sb\utils;

use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;

final class MathUtils {
	public static function midpoint(AxisAlignedBB $aaBB) : Vector3 {
		return new Vector3(($aaBB->maxX + $aaBB->minX) / 2, ($aaBB->maxY + $aaBB->minY) / 2, ($aaBB->maxZ + $aaBB->minZ) / 2);
	}

	public static function min(AxisAlignedBB $aaBB) : Vector3 {
		return new Vector3($aaBB->minX, $aaBB->minY, $aaBB->minZ);
	}

	public static function max(AxisAlignedBB $aaBB) : Vector3 {
		return new Vector3($aaBB->maxX, $aaBB->maxY, $aaBB->maxZ);
	}

	public static function fromCoordinates(Vector3 ...$positions) : AxisAlignedBB {
		$minX = PHP_INT_MAX;
		$maxX = PHP_INT_MIN;
		$minY = PHP_INT_MAX;
		$maxY = PHP_INT_MIN;
		$minZ = PHP_INT_MAX;
		$maxZ = PHP_INT_MIN;
		foreach($positions as $pos) {
			if($pos->x < $minX)
				$minX = $pos->x;
			if($pos->x > $maxX)
				$maxX = $pos->x;
			if($pos->y < $minY)
				$minY = $pos->y;
			if($pos->y > $maxY)
				$maxY = $pos->y;
			if($pos->z < $minZ)
				$minZ = $pos->z;
			if($pos->z > $maxZ)
				$maxZ = $pos->z;
		}
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}

	public static function chance(float $chance): bool
	{
		$string = strrchr(strval($chance), ".");
		if ($string == false) {
			return mt_rand(1, 100) <= $chance;
		}
		$count = strlen(substr($string, 1));
		$multiply = intval("1" . str_repeat("0", $count));
		return mt_rand(1, (100 * $multiply)) <= ($chance * $multiply);
	}

	#[ArrayShape(['d' => "int", 'h' => "int", 'm' => "int", 's' => "int"])]
	public static function secondsToTime(int $inputSeconds) : array{
		$secondsInAMinute = 60;
		$secondsInAnHour = 60 * $secondsInAMinute;
		$secondsInADay = 24 * $secondsInAnHour;

		$days = floor($inputSeconds / $secondsInADay);

		$hourSeconds = $inputSeconds % $secondsInADay;
		$hours = floor($hourSeconds / $secondsInAnHour);

		$minuteSeconds = $hourSeconds % $secondsInAnHour;
		$minutes = floor($minuteSeconds / $secondsInAMinute);

		$remainingSeconds = $minuteSeconds % $secondsInAMinute;
		$seconds = ceil($remainingSeconds);

		return ['d' => (int)$days, 'h' => (int)$hours, 'm' => (int)$minutes, 's' => (int)$seconds,];
	}

	#[Pure]
	public static function getFormattedTime(int $seconds): string {
		$string = "";

		foreach (self::secondsToTime($seconds) as $k => $v) {
			if ($v > 0) {
				if ($string === "") {
					$string .= $v . $k;
				} else $string .= " " . $v . $k;
			}
		}

		return $string;
	}

	public static function getFullyFormattedTime(int $seconds): string {
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
	}

	public static function getRandomFloat(int $min, int $max): float{
		return mt_rand($min, $max - 1) + (mt_rand(0, PHP_INT_MAX - 1) / PHP_INT_MAX);
	}

	public static function intToPrefix($input) : string {
		if (!is_numeric($input)) return "0";
		$suffixes = array("", "K", "M", "B", "T", "QD", "QT");
		$suffixIndex = 0;
		while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)) {
			$suffixIndex++;
			$input /= 1000;
		}
		return (
			$input > 0
				? floor($input * 1000) / 1000
				: ceil($input * 1000) / 1000
			)
			. $suffixes[$suffixIndex];
	}

    public static function getDays(int $time): string{
		$days = floor($time / 86400);

		return "$days";
	}

	public static function getHours(int $time): string{
		$hours = floor(($time / 3600) % 24);

		return "$hours";
	}

	public static function getMinutes(int $time): string{
		$minutes = floor(($time / 60) % 60);

		return "$minutes";
	}

	public static function getSeconds(int $time): string{
		$seconds = floor($time % 60);

		return "$seconds";
	}
}