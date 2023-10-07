<?php

namespace sb\player\ability;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;
use sb\entity\CustomFallingBlock;
use sb\entity\LightningBolt;
use sb\item\CustomItem;
use sb\item\CustomItems;
use sb\item\sets\SetWeapon;
use sb\player\CorePlayer;
use sb\scheduler\player\BlocksReplaceTask;
use sb\Skyblock;

class AbilityManager {
    private static array $abilities = [];

    public function __construct() {
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Cupid", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				$player->teleport($args[0][0] ?? $player->getPosition());
			}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Xmas", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new BlocksReplaceTask($player), 20 * 10);
			}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Fantasy", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{}

			public function attack(EntityDamageEvent $event): bool{
				if (!parent::attack($event)) return false;

				if ($event instanceof EntityDamageByEntityEvent) {
					$attacker = $event->getDamager();
					$entity = $event->getEntity();
					if ($attacker instanceof CorePlayer && $entity instanceof CorePlayer) {
						if ($attacker->getPosition()->distance($entity->getPosition()) < 0.5) {
							$event->setModifier(($event->getFinalDamage() * 0.5), DamageInfo::CUSTOM_MODIFIER);
						} else {
							$distance = $attacker->getPosition()->distance($entity->getPosition()) / 100;
							$event->setModifier(($event->getFinalDamage() * $distance * 15), DamageInfo::CUSTOM_MODIFIER);
						}
					}
				}
				return true;
			}
		});
		self::register(new class() extends BaseAbility {
			private array $abilityCooldown = [];

			public function __construct(){
				parent::__construct("Reaper", 25, 60 * 3);
			}

			public function attack(EntityDamageEvent $event): bool{
				if (!parent::attack($event)) return false;

				if ($event instanceof EntityDamageByEntityEvent) {
					if (!$event->getDamager() instanceof CorePlayer) return false;
					$name = $event->getDamager()->getName();
					$cooldown = $this->abilityCooldown[$name] ?? null;

					if ($cooldown === null) {
						$this->abilityCooldown[$name] = $this->getName();
						$event->setBaseDamage(mt_rand(5, 9) * 2);
						Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($name): void{
							unset($this->abilityCooldown[$name]);
						}), 10 * 20);
						return true;
					}
				}
				return true;
			}

			public function react(CorePlayer $player, ...$args): void{}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Spooky", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				$attacker = $args[0][0] ?? null;

				if ($attacker instanceof CorePlayer) {
					$helmet = $attacker->getArmorInventory()->getHelmet();
					$attacker->getArmorInventory()->setHelmet(VanillaBlocks::CARVED_PUMPKIN()->asItem());
					$attacker->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 6, 10));
					$attacker->getEffects()->add(new EffectInstance(VanillaEffects::MINING_FATIGUE(), 20 * 6, 5));

					Skyblock::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function () use ($attacker, $helmet): void{
						$attacker->getArmorInventory()->setHelmet($helmet);
						throw new CancelTaskException();
					}), 20 * 6, 20 * 6);
				}
			}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Thor", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				$damage = 12;
				$time = 5;
				$item = $player->getInventory()->getItemInHand();
				$item2 = CustomItems::THOR_AXE()->getItem();

				if ($item instanceof $item2) {
					$damage *= 1.25;
					$time += 3;
				}
				$entity = new LightningBolt($player->getLocation(), $damage, null, $time);
				$entity->setOwningEntity($player);
				$entity->spawnToAll();
			}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Traveler", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				$pos = $player->getPosition();
				$pos->y += 6;
				$positions = $this->getPositions($pos);
				$blocks = [VanillaBlocks::SOUL_SAND(), VanillaBlocks::END_STONE(), VanillaBlocks::NETHER_BRICKS()];

				$i = 0;
				Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($player, &$i, $positions, $blocks): void{
					if (++$i >= 4) throw new CancelTaskException();

					/** @var Position $position */
					foreach ($positions as $position) {
						$entity = new CustomFallingBlock($position, $blocks[array_rand($blocks)]);
						$entity->setOwningEntity($player);
						$entity->spawnToAll();
					}
				}), 20 * 2);
			}

			private function getPositions(Position $middle): array{
				$positions = [$middle];
				$x = [];

				for ($i = 1; $i <= 4; $i++) {
					$x[] = $middle->add($i, 0, 0);
					$x[] = $middle->subtract($i, 0, 0);

					$positions[] = $middle->add(0, 0, $i);
					$positions[] = $middle->subtract(0, 0, $i);
				}

				foreach ($x as $position) {
					for ($i = 1; $i <= 4; $i++) {
						$positions[] = $position->subtract(0, 0, $i);
						$positions[] = $position->add(0, 0, $i);
					}

					$positions[] = $position;
				}
				$locations = [];
				foreach ($positions as $position) {
					$locations[] = new Location($position->getX(), $position->getY(), $position->getZ(), $middle->getWorld(), 0, 0);
				}

				return $locations;
			}
		});
		self::register(new class() extends BaseAbility {
			public function __construct(){
				parent::__construct("Yijki", 25, 60 * 3);
			}

			public function react(CorePlayer $player, ...$args): void{
				$damage = 12;
				$item = $player->getInventory()->getItemInHand();
				if(($string = $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
					$item = CustomItems::fromString($string);

					if ($item instanceof SetWeapon) {
						if ($item->getWeaponType() === $this->getName()) $damage *= 1.25;
					}
				}
				$entity = new LightningBolt($player->getLocation(), $damage);
				$entity->setOwningEntity($player);
				$entity->spawnToAll();
			}
		});
    }

    private static function register(BaseAbility $ability): void{
        self::$abilities[strtolower($ability->getName())] = $ability;
    }

	public static function getAll(): array{
		return self::$abilities;
	}

    public static function get(string $ability): ?BaseAbility{
        return self::$abilities[strtolower($ability)] ?? null;
    }
}