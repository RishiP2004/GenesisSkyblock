<?php

namespace sb\item;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\CloningRegistryTrait;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use sb\item\enchantment\CustomEnchantments;
use sb\item\listeners\ItemDamageListener;
use sb\item\sets\DirectAppliableArmor;
use sb\item\sets\SetArmor;
use sb\item\sets\SetWeapon;
use sb\item\utils\ArmorUtils;
use sb\player\ability\AbilityManager;
use sb\player\CorePlayer;
use sb\utils\MathUtils;
use sb\item\utils\DamageInfo;
use sb\block\MonsterSpawner;
//todo: remove usage of ghost classes
/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see \pocketmine\utils\RegistryUtils::_generateMethodAnnotations()
 *
 * @method static ChunkCollector CHUNKCOLLECTOR()
 * @method static BankNote BANKNOTE()
 * @method static CrateKey CRATEKEY()
 * @method static ImmortalScroll IMMORTAL_SCROLL()
 * @method static MoneyPouch MONEYPOUCH()
 * @method static SellWand SELLWAND()
 * @method static XpBottle XPBOTTLE()
 * @method static SlotBotTicket SLOTBOT_TICKET()
 * @method static VoteCrate VOTECRATE()
 * @method static FishingBait FISHING_BAIT()
 * @method static PermNote PERM_NOTE()
 * @method static Lootbox LOOTBOX()
 * @method static PotionPouch POTIONPOUCH()
 * @method static ClickableKit CLICKABLE_KIT()
 * @method static WhiteScroll WHITE_SCROLL()
 *
 * @method static SetWeapon KOTH_AXE()
 * @method static SetWeapon KOTH_SWORD()
 * @method static SetWeapon CUPID_BOW()
 * @method static SetWeapon DRAGON_SWORD()
 * @method static SetWeapon PHANTOM_SWORD()
 * @method static SetWeapon RANGER_BOW()
 * @method static SetWeapon REAPER_SWORD()
 * @method static SetWeapon SUPREME_SWORD()
 * @method static SetWeapon THOR_AXE()
 * @method static SetWeapon TRAVELER_AXE()
 * @method static SetWeapon YETI_MAUL()
 * @method static SetWeapon YIJKI_AXE()
 *
 * @method static SetArmor CUPID_HELMET()
 * @method static SetArmor CUPID_CHESTPLATE()
 * @method static SetArmor CUPID_LEGGINGS()
 * @method static SetArmor CUPID_BOOTS()
 * @method static SetArmor DRAGON_HELMET()
 * @method static SetArmor DRAGON_CHESTPLATE()
 * @method static SetArmor DRAGON_LEGGINGS()
 * @method static SetArmor DRAGON_BOOTS()
 * @method static SetArmor FANTASY_HELMET()
 * @method static SetArmor FANTASY_CHESTPLATE()
 * @method static SetArmor FANTASY_LEGGINGS()
 * @method static SetArmor FANTASY_BOOTS()
 * @method static SetArmor KOTH_HELMET()
 * @method static SetArmor KOTH_CHESTPLATE()
 * @method static SetArmor KOTH_LEGGINGS()
 * @method static SetArmor KOTH_BOOTS()
 * @method static SetArmor PHANTOM_HELMET()
 * @method static SetArmor PHANTOM_CHESTPLATE()
 * @method static SetArmor PHANTOM_LEGGINGS()
 * @method static SetArmor PHANTOM_BOOTS()
 * @method static SetArmor RANGER_HELMET()
 * @method static SetArmor RANGER_CHESTPLATE()
 * @method static SetArmor RANGER_LEGGINGS()
 * @method static SetArmor RANGER_BOOTS()
 * @method static SetArmor REAPER_HELMET()
 * @method static SetArmor REAPER_CHESTPLATE()
 * @method static SetArmor REAPER_LEGGINGS()
 * @method static SetArmor REAPER_BOOTS()
 * @method static SetArmor SPOOKY_HELMET()
 * @method static SetArmor SPOOKY_CHESTPLATE()
 * @method static SetArmor SPOOKY_LEGGINGS()
 * @method static SetArmor SPOOKY_BOOTS()
 * @method static SetArmor SUPREME_HELMET()
 * @method static SetArmor SUPREME_CHESTPLATE()
 * @method static SetArmor SUPREME_LEGGINGS()
 * @method static SetArmor SUPREME_BOOTS()
 * @method static SetArmor THOR_HELMET()
 * @method static SetArmor THOR_CHESTPLATE()
 * @method static SetArmor THOR_LEGGINGS()
 * @method static SetArmor THOR_BOOTS()
 * @method static SetArmor INTERDIMENSIONAL_HELMET()
 * @method static SetArmor INTERDIMENSIONAL_CHESTPLATE()
 * @method static SetArmor INTERDIMENSIONAL_LEGGINGS()
 * @method static SetArmor INTERDIMENSIONAL_BOOTS()
 * @method static SetArmor XMAS_HELMET()
 * @method static SetArmor XMAS_CHESTPLATE()
 * @method static SetArmor XMAS_LEGGINGS()
 * @method static SetArmor XMAS_BOOTS()
 * @method static SetArmor YETI_HELMET()
 * @method static SetArmor YETI_CHESTPLATE()
 * @method static SetArmor YETI_LEGGINGS()
 * @method static SetArmor YETI_BOOTS()
 * @method static SetArmor YIJKI_HELMET()
 * @method static SetArmor YIJKI_CHESTPLATE()
 * @method static SetArmor YIJKI_LEGGINGS()
 * @method static SetArmor YIJKI_BOOTS()
 *
 * @method static MonsterSpawner MONSTER_SPAWNER()
 */
class CustomItems {
	use CloningRegistryTrait;

	private static int $spawnerRuntimeId = 0;

	protected static function setup(): void {
		self::register(CustomItemIds::CHUNKCOLLECTOR, new ChunkCollector());
		self::register(CustomItemIds::BANKNOTE,new BankNote());
		self::register(CustomItemIds::CRATEKEY,new CrateKey());
		self::register(CustomItemIds::MONEYPOUCH,new MoneyPouch());
		self::register(CustomItemIds::SELLWAND,new SellWand());
		self::register(CustomItemIds::XPBOTTLE,new XpBottle());
		self::register(CustomItemIds::SLOTBOT_TICKET,new SlotbotTicket());
		self::register(CustomItemIds::VOTE_CRATE,new VoteCrate());
		self::register(CustomItemIds::FISHING_BAIT,new FishingBait());
		self::register(CustomItemIds::PERM_NOTE,new PermNote());
		self::register(CustomItemIds::CLICKABLE_KIT, new ClickableKit());
		self::register(CustomItemIds::POTIONPOUCH, new PotionPouch());
		self::register(CustomItemIds::IMMORTAL_SCROLL, new ImmortalScroll());
		self::register(CustomItemIds::LOOTBOX, new Lootbox());
		self::register(CustomItemIds::WHITE_SCROLL, new WhiteScroll());
		//set weapons
		self::register("koth_axe", new class extends SetWeapon implements ItemDamageListener {
			public function __construct() {
				$i = VanillaItems::DIAMOND_AXE()->setCustomName("§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fAxe");
				$i->setLore([
					"§r",
					"§l§cK§6.§eO§a.§bT§5.§dH §fWEAPON BONUS",
					"§r§f* §r§fDeal +50% Durability Damage.",
					"§r§7(Requires all 4 koth items.)"
				]);
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5));
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
				parent::__construct("Koth Axe", "koth_axe", $i);
			}

			public function onDamage(Item $item, CorePlayer $damager, EntityDamageEvent $event) : void {
				$this->damageArmor(($event->getFinalDamage() * 1.5), $event->getEntity());
			}
		});
		self::register("koth_sword",new SetWeapon(
			"KothSword",
			"koth_sword",
			VanillaItems::DIAMOND_SWORD()
				->setCustomName("§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fSword")
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
				->setLore([
					"§r",
					"§l§cK§6.§eO§a.§bT§5.§dH §fWEAPON BONUS",
					"§r§f* §r§fDeal +7.5% damage to all enemies.",
					"§r§7(Requires all 4 koth items.)"
				]),
			new DamageInfo([
			"increase" => [
				"default" => 0.075
			]])
		));
		self::register("cupid_bow", new class extends SetWeapon {
			public function __construct() {
				$i = VanillaItems::BOW()->setCustomName("§l§dCupid Bow");
				$i->setLore([
					"§r",
					"§l§dCUPID WEAPON BONUS",
					"§r§d* Deal +50% Durability Damage.",
					"§r§7(Requires all 4 cupid items.)"
				]);
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5));
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
				parent::__construct("Cupid Bow", "cupid_bow", $i);
			}

			public function attack(EntityDamageEvent $event): void{
				if ($event instanceof EntityDamageByEntityEvent) {
					if ($event->getDamager() instanceof Arrow) {
						$event->setBaseDamage($event->getFinalDamage() +10);
						AbilityManager::get("cupid")?->attemptReact($event->getDamager()->getOwningEntity(), [$event->getEntity()->getPosition()]);
					}
				}
			}
		});
		self::register("dragon_sword", new SetWeapon(
			"Dragon Sword",
			"dragon_sword",
			VanillaItems::DIAMOND_SWORD()
				->setCustomName("§l§eDragon Sword")
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
				->setLore([
					"§r",
					"§l§eDRAGON WEAPON BONUS",
					"§r§e* Deal +20% damage to all enemies.",
					"§r§7(Requires all 4 supreme items.)"
				]),
			new DamageInfo([
				"increase" => [
					"default" => 0.2
				]]
			)
		));
		self::register("phantom_sword", new SetWeapon(
			"Phantom Sword",
			"phantom_sword",
			VanillaItems::DIAMOND_SWORD()
				->setCustomName("§l§cPhantom Scythe")
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
				->setLore([
					"§r",
					"§l§cPHANTOM WEAPON BONUS",
					"§r§c* §r§cDeal +5% damage to all enemies.",
					"§r§c* §r§cTake -5% damage from all enemies.",
					"§r§7(Requires all 4 phantom items.)"
				]),
			new DamageInfo([
				"increase" => [
					"default" => 0.05
				],
				"decrease" => [
					"default" => 0.05
				]
			])
		));
		self::register("ranger_bow", new SetWeapon(
			"Ranger Bow",
			"ranger_bow",
			VanillaItems::BOW()
				->setCustomName("§l§aRanger Bow")
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 2))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1))
				->setLore([
					"§r",
					"§l§aRANGER WEAPON BONUS",
					"§r§a* §r§aRanger bow grants +30% increased bow damage.",
					"§r§7(Requires all 4 ranger items.)"
				]),
			new DamageInfo([
				"increase" => [
					"default" => 0.30
				]
			])
		));
		self::register("reaper_sword", new SetWeapon(
			"Reaper Sword",
			"reaper_sword",
			VanillaItems::DIAMOND_SWORD()
				->setCustomName("§l§4Reaper Scythe")
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 2))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1))
				->setLore([
					"§r",
					"§l§4REAPER WEAPON BONUS",
					"§r§4* §r§4Deal +20% damage to all enemies.",
					"§r§7(Requires all 4 reaper items.)"
				]),
			new DamageInfo([
					"increase" => [
						"default" => 0.2
					]
				])
			)
		);
		self::register("supreme_sword", new SetWeapon(
				"Supreme Sword",
				"supreme_sword",
				VanillaItems::DIAMOND_SWORD()
					->setCustomName("§l§4Supreme Fanny Pack")
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§4SUPREME WEAPON BONUS",
						"§r§4* §r§4Deal +20% damage to all enemies.",
						"§r§4* §r§4Enemies deal -10% less damage to you.",
						"§r§7(Requires all 4 supreme items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.2
					],
					"decrease" => [
						"default" => 0.1
					]
				])
			)
		);
		self::register("thor_axe", new SetWeapon(
				"Thor Axe",
				"thor_axe",
				VanillaItems::DIAMOND_AXE()
					->setCustomName("§l§bMjolnir")
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§bTHOR WEAPON BONUS",
						"§r§b* Deal +20% damage to all enemies.",
						"§r§7(Requires all 4 thor items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.2
					]
				])
			)
		);
		self::register("traveler_axe", new SetWeapon(
				"Traveler Axe",
				"traveler_axe",
				VanillaItems::DIAMOND_AXE()
					->setCustomName("§l§5Time Splitter")
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§5TRAVELER WEAPON BONUS",
						"§r§5* §r§5Deal +10% damage to all enemies.",
						"§r§7(Requires all 4 dimensional traveler items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.1
					]
				])
			)
		);
		self::register("yeti_maul", new class extends SetWeapon {
			public function __construct() {
				$i = VanillaItems::DIAMOND_AXE()->setCustomName("§l§bYeti Maul");
				$i->setLore([
					"§r",
					"§l§bYETI WEAPON BONUS",
					"§r§b* Deal +75% durability damage.",
					"§r§b* Deal +7.5% damage to all enemies.",
					"§r§7(Requires all 4 yeti items.)"
				]);
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5));
				$i->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
				parent::__construct("YetiAxe", "yeti_axe", $i, new DamageInfo(["increase" => ["default" => 0.075]]));
			}

			public function attack(EntityDamageEvent $event): void{
				parent::attack($event);
				$entity = $event->getEntity();

				if ($entity instanceof CorePlayer) {
					$this->damageArmor((($event->getFinalDamage() * 1.075) * 1.75), $entity);
				}
			}
		});
		self::register("yijki_axe", new SetWeapon(
				"Yijki Axe",
				"yijki_axe",
				VanillaItems::DIAMOND_AXE()
					->setCustomName("§l§fYijki's World Ender")
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§fYIJKI WEAPON BONUS",
						"§r§f* §r§fDeal +20% damage to all enemies.",
						"§r§f* §r§f+125% Revenge of yijki Ability",
						"§r§7(Requires all 4 yijki items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.2
					]
				])
			)
		);

		$armors = [
			VanillaItems::DIAMOND_HELMET(),
			VanillaItems::DIAMOND_CHESTPLATE(),
			VanillaItems::DIAMOND_LEGGINGS(),
			VanillaItems::DIAMOND_BOOTS()
		];

		foreach($armors as $armor) {
			$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
			self::register("cupid_". $type, new SetArmor(
				"Cupid " . ucfirst($type),
				"cupid_" . $type,
				clone $armor
					->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§dCupid Helmet",
						"chestplate" => "§l§dCupid Chestplate",
						"leggings" => "§l§dCupid Leggings",
						"boots" => "§l§dCupid Boots",
					})
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§dCUPID SET BONUS",
						"§r§d* Deal +30% more damage to all enemies.",
						"§r§d* Take -15% less damage from enemies ",
						"§r§d* 58% less combat tag duration",
						"§r§d* Cupid Bow Teleportation Passive Ability",
						"§r§7(Requires all 4 cupid items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.3
					],
					"decrease" => [
						"default" => 0.2
					]
				])
			));
			self::register("dragon_" . $type, new SetArmor(
				"Dragon " . ucfirst($type),
				"dragon_" . $type,
				clone $armor
					->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§eDecapitated Dragon Skull",
						"chestplate" => "§l§eFiery Chestplate of Dragons",
						"leggings" => "§l§eScorched Leggings of Dragons",
						"boots" => "§l§eDragon Slayer Battle Boots",
					})
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§eDRAGON SET BONUS",
						"§r§e* +15% PvP Damage",
						"§r§e* Take -20% less damage from enemies",
						"§r§7(Requires all 4 dragon items.)"
					]),
				new DamageInfo([
					"increase" => [
						Player::class => 0.15
					],
					"decrease" => [
						"default" => 0.2
					]
				])
			));
			self::register("fantasy_" . $type, new class (clone $armor) extends SetArmor implements DirectAppliableArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§2Fantasy Helmet",
						"chestplate" => "§l§2Fantasy Chestplate",
						"leggings" => "§l§2Fantasy Leggings",
						"boots" => "§l§2Fantasy Boots",
					});
					$armor->setLore([
						"§r",
						"§l§2Fantasy SET BONUS",
						"§r§2* Gears IV",
						"§r§2* Deal +25% more damage to all enemies.",
						"§r§2* 10% Critical Strike Chance",
						"§r§2* Fantasy Trap Passive Ability",
						"§r§7(Requires all 4 fantasy items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct("Fantasy " . ucfirst($type), "fantasy_".$type, $armor, new DamageInfo(["increase" => ["default" => 0.25]]));
				}
				public function applyFullArmor(Player $player) : void{
					$player->setMovementSpeed(0.1);
					$player->setMovementSpeed($player->getMovementSpeed() * (1 + 0.2 * 4));
				}

				public function attack(EntityDamageEvent $event): void{
					parent::attack($event);
					AbilityManager::get("fantasy")?->attack($event);
				}
			});
			self::register("koth_". $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fHelmet",
						"chestplate" => "§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fChestplate",
						"leggings" => "§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fLeggings",
						"boots" => "§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH §f§k|§r §l§fBoots"
					});
					$armor->setLore([
						'§r',
						'§l§dKOTH SET BONUS',
						'§l§d* §r§d+20% PvP Damage',
						'§l§d* §r§d+50% PvE Damage',
						'§l§d* §r§dNo Fall Damage',
						'§r§7(Requires all 4 koth items.)'
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct("Koth " . ucfirst($type), "koth_" .$type, $armor,
						new DamageInfo([
							"increase" => [
								Player::class => 0.2,
								"default" => 0.5
							],
							"decrease" => [
								"default" => 0.2
							]
						]
						)
					);
				}

				public function defend(EntityDamageEvent $event): void{
					if ($event->getCause() == $event::CAUSE_FALL) {
						$event->cancel();
						return;
					}
					parent::defend($event);
				}
			});
			self::register("phantom_" . $type, new SetArmor(
				"Phantom " . ucfirst($type),
				"phantom_" . $type,
				clone $armor
					->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§cPhantom Hood",
						"chestplate" => "§l§cPhantom Shroud",
						"leggings" => "§l§cPhantom Robeset",
						"boots" => "§l§cPhantom Sandals",
					})
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§cPHANTOM SET BONUS",
						"§r§c* §r§cDeal +35% more damage to all enemies.",
						"§r§c* §r§cTake -10% damage from all enemies.",
						"§r§7(Requires all 4 phantom items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.35
					],
					"decrease" => [
						"default" => 0.1
					]
				])
			));
			self::register("ranger_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§aRanger Hood",
						"chestplate" => "§l§cRanger Shroud",
						"leggings" => "§l§cRanger Robeset",
						"boots" => "§l§cRanger Sandals",
					});
					$armor->setLore([
						"§r",
						"§l§aRANGER SET BONUS",
						"§r§a* §r§aEnemies bows do -25% less damage to you.",
						"§r§a* §r§aRanger bow grants +30% increased bow damage.",
						"§r§7(Requires all 4 ranger items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct("Ranger " . ucfirst($type), "ranger_" . $type, $armor, new DamageInfo(["decrease" => [Arrow::class => 0.25]]));
				}

				public function defend(EntityDamageEvent $event): void{
					if ($event instanceof EntityDamageByChildEntityEvent) {
						parent::defend($event);
					}
				}
			});
			self::register("reaper_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§4Reaper Helmet",
						"chestplate" => "§l§4Reaper Chestplate",
						"leggings" => "§l§4Reaper Leggings",
						"boots" => "§l§4Reaper Boots",
					});
					$armor->setLore([
						"§r",
						"§l§4Reaper SET BONUS",
						"§r§4* Deal +30% more damage to all enemies.",
						"§r§4* Take 15% less damage from enemies",
						"§r§4* Mark of the Reaper Passive Ability",
						"§r§7(Requires all 4 reaper items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Reaper " . ucfirst($type),
						"reaper_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.3
							],
							"decrease" => [
								"default" => 0.15
							]
						])
					);
				}

				public function attack(EntityDamageEvent $event): void{
					parent::attack($event);
					AbilityManager::get("reaper")?->attack($event);
				}
			});
			self::register("spooky_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§6Spooky Hood",
						"chestplate" => "§l§6Spooky Chestplate",
						"leggings" => "§l§6Spooky Leggings",
						"boots" => "§l§6Spooky Boots",
					});
					$armor->setLore([
						"§r",
						"§l§6Spooky SET BONUS",
						"§r§6* Deal +20% more damage to all enemies.",
						"§r§6* Take -20% less damage from enemies",
						"§r§6* Halloweenify passive ability",
						"§r§7(Requires all 4 spooky items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Spooky " . ucfirst($type),
						"spooky_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.2
							],
							"decrease" => [
								"default" => 0.25
							]
						])
					);
				}

				public function defend(EntityDamageEvent $event): void{
					parent::defend($event);

					if ($event instanceof EntityDamageByEntityEvent) {
						AbilityManager::get("spooky")?->attemptReact($event->getEntity(), [$event->getDamager()]);
					}
				}
			});
			self::register("supreme_" . $type,new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§4Supreme Headgear",
						"chestplate" => "§l§4Supreme Vest",
						"leggings" => "§l§4Supreme Chaps",
						"boots" => "§l§4Supreme Thruster Boots",
					});
					$armor->setLore([
						"§r",
						"§l§4SUPREME SET BONUS",
						"§r§4* §r§4No Fall Damage / Food Loss",
						"§r§4* §r§4Deal +15% damage to all enemies",
						"§r§4* §r§4Enemy arrows deal +10% more damage to you",
						"§r§4* §r§4+200% clout",
						"§r§4* §r§4Chance to give Slowness I for 5s",
						" §4when hitting an enemy from behind",
						"§r§7(Requires all 4 supreme items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Supreme" . ucfirst($type),
						"supreme_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.15
							],
							"decrease" => [
								Arrow::class => 0.10
							]
						])
					);
				}

				public function attack(EntityDamageEvent $event): void{
					parent::attack($event);

					$entity = $event->getEntity();
					if ($event instanceof EntityDamageByEntityEvent) {
						if ($event->getDamager()->getDirectionVector()->dot($entity->getDirectionVector()) > 0) {
							if (MathUtils::getRandomFloat(0, 100) <= 5) {
								if ($entity instanceof Living) {
									$entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5));
								}
							}
						}
					}
				}

				public function defend(EntityDamageEvent $event): void{
					if ($event->getCause() == $event::CAUSE_FALL) {
						$event->cancel();
						return;
					}
					parent::defend($event);
				}
			});
			self::register("thor_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§bThor Helmet",
						"chestplate" => "§l§bThor Chestplate",
						"leggings" => "§l§bThor Leggings",
						"boots" => "§l§bThor Boots",
					});
					$armor->setLore([
						"§r",
						"§l§bThor SET BONUS",
						"§r§b* Take -15% less damage from enemies",
						"§r§b* 25% less combat tag duration",
						"§r§b* Mjolnir Passive Ability",
						"§r§7(Requires all 4 thor items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Thor" . ucfirst($type),
						"thor_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.15
							],
							"decrease" => [
								"default" => 0.25
							]
						])
					);
				}

				public function defend(EntityDamageEvent $event): void{
					parent::defend($event);
					if ($event instanceof EntityDamageByEntityEvent && $event->getEntity() instanceof Player) {
						AbilityManager::get("thor")?->attemptReact($event->getEntity());
					}
				}
			});
			self::register("interdimensional_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§5Interdimensional Hood",
						"chestplate" => "§l§5Chestplate of Ad Infinitum",
						"leggings" => "§l§5Timeless Robes",
						"boots" => "§l§5Warp Speed Sandals",
					});
					$armor->setLore([
						"§r",
						"§l§5TRAVELER SET BONUS",
						"§r§5* §r§5You deal +30% more damage.",
						"§r§5* §r§5Dimensional Shift Passive Ability ",
						"§r§7(Requires all 4 dimensional traveler items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Traveler " . ucfirst($type),
						"traveler_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.3
							],
							"decrease" => [
								Arrow::class => 0.1
							]
						])
					);
				}

				public function attack(EntityDamageEvent $event): void{
					$entity = $event->getEntity();
					if ($event instanceof EntityDamageByEntityEvent) {
						if ($event->getDamager()->getDirectionVector()->dot($entity->getDirectionVector()) > 0) {
							if (MathUtils::getRandomFloat(0, 100) <= 5) {
								if ($entity instanceof Living) {
									$entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5));
								}
							}
						}
					}

					parent::attack($event);
				}

				public function defend(EntityDamageEvent $event): void{
					$entity = $event->getEntity();
					if ($event instanceof EntityDamageByEntityEvent) {
						$attacker = $event->getDamager();
						if ($attacker instanceof Player && $entity instanceof Player) {
							AbilityManager::get("traveler")?->attemptReact($entity);
						}
					}
				}
			});
			self::register("xmas_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§cX§2M§aA§fS Helmet",
						"chestplate" => "§l§cX§2M§aA§fS Chestplate",
						"leggings" => "§l§cX§2M§aA§fS Leggings",
						"boots" => "§l§cX§2M§aA§fS Boots",
					});
					$armor->setLore([
						"§r",
						"§l§l§cX§2M§aA§fS SET BONUS",
						"§r§2* Deal +20% more damage to all enemies.",
						"§r§2* Take -15% less damage from enemies",
						"§r§2* Active Snowify Ability",
						"§r§7(Requires all 4 xmas items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Xmas " . ucfirst($type),
						"xmas_" . $type,
						$armor,
						new DamageInfo([
							"increase" => [
								"default" => 0.2
							],
							"decrease" => [
								"default" => 0.15
							]
						])
					);
				}

				public function defend(EntityDamageEvent $event): void{
					parent::defend($event);
					if ($event instanceof EntityDamageByEntityEvent && $event->getEntity() instanceof Player) {
						AbilityManager::get("xmas")?->attemptReact($event->getEntity());
					}
				}
			});
			self::register("yeti_" . $type, new SetArmor(
				"Yeti " . ucfirst($type),
				"yeti_". $type,
				clone $armor
					->setCustomName(match (strtolower($type)) {
						"helmet" => "§l§bYeti Facemask",
						"chestplate" => "§l§bBloody Yeti Torso",
						"leggings" => "§l§bFuzzy Yeti Leggings",
						"boots" => "§l§bBig-Yeti boots",
					})
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4))
					->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3))
					->setLore([
						"§r",
						"§l§bYETI SET BONUS",
						"§r§b* §r§bDeal +10% more damage to all enemies,",
						"§r§b* §r§bEnemies deal -10% less damage to you.",
						"§r§7(Requires all 4 yeti items.)"
					]),
				new DamageInfo([
					"increase" => [
						"default" => 0.1
					],
					"decrease" => [
						"default" => 0.1
					]
				])
			));
			self::register("yijki_" . $type, new class (clone $armor) extends SetArmor {
				public function __construct($armor) {
					$type = ArmorUtils::armorSlotToType($armor->getArmorSlot());
					$armor->setCustomName(match (strtolower($type)) {
						"helmet" => "§r§l§fMask of Yijki the Destroyer of Worlds",
						"chestplate" => "§r§l§fMantle of Yijki the Destroyer of Worlds",
						"leggings" => "§r§l§fRobeset of Yijki the Destroyer of Worlds",
						"boots" => "§r§l§fFootwraps of Yijki the Destroyer of Worlds",
					});
					$armor->setLore([
						"§r",
						"§l§fYIJKI SET BONUS",
						"§r§f* §r§fEnemies deal -30% less damage to you.",
						"§r§f* §r§fRevenge Of Yijki Passive Ability",
						"§r§7(Requires all 4 yijki items.)"
					]);
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
					$armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
					parent::__construct(
						"Yijki " . ucfirst($type),
						"yijki_" . $type,
						$armor,
						new DamageInfo([
							"decrease" => [
								"default" => 0.3
							]
						])
					);
				}

				public function defend(EntityDamageEvent $event): void{
					parent::defend($event);

					$entity = $event->getEntity();
					if ($event instanceof EntityDamageByEntityEvent) {
						$attacker = $event->getDamager();
						if ($attacker instanceof Player && $entity instanceof Player) {
							AbilityManager::get("yijki")?->attemptReact($entity);
						}
					}
				}
			});
		}
		self::registerOverriders();
	}

	protected static function register(string $name, CustomItem $item) : void {
		self::_registryRegister($name, $item);
	}

	public static function fromString(string $name): CustomItem {
		$result = self::_registryFromString($name);
		assert($result instanceof CustomItem);
		return $result;
	}

	/**
	 * @return CustomItem[]
	 */
	public static function getAll(): array {
		return self::_registryGetAll();
	}

	private static function registerOverriders() : void {
		$fishingRod = new FishingRod(new ItemIdentifier(ItemTypeIds::FISHING_ROD), "Fishing Rod");
		ItemOverrider::override("fishing_rod", $fishingRod);
		ItemOverrider::setDeserializer(ItemTypeNames::FISHING_ROD, $fishingRod, function () use ($fishingRod) {
			return clone $fishingRod;
		});
		ItemOverrider::setSerializer(ItemTypeNames::FISHING_ROD, $fishingRod);
		ItemOverrider::resetCreativeInventory();

		self::_registryRegister('monster_spawner',
			new MonsterSpawner(new BlockIdentifier(self::$spawnerRuntimeId = BlockTypeIds::MONSTER_SPAWNER, \sb\block\tile\MonsterSpawner::class),
				'SB Monster Spawner',
				new BlockTypeInfo(new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()))));
	}

	public static function initHack() :void {
		static $nameToMeta = [
			10 => "Chicken",
			11 => "Cow",
			12 => "Pig",
			17 => "Squid",
			20 => "Iron Golem",
			32 => "Zombie",
			36 => "Zombie Pigman",
			34 => "Skeleton",
			37 => "Slime",
			16 => "Mooshroom",
			43 => "Blaze",
		];

		foreach($nameToMeta as $meta => $name){
			StringToItemParser::getInstance()->override($name . "_spawner", fn() => self::setSpawnerEntityId(
				self::MONSTER_SPAWNER()->asItem(), $meta
			)->setCustomName(
				TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "{$name} Spawner"
			));

		}
	}

	public static function setSpawnerEntityId(Item $item, Int $id) : Item{
		$namedtag = $item->getNamedTag();
		$namedtag->setInt('SpawnerEntityId', $id);
		$item->setNamedTag($namedtag);
		return $item;
	}
}