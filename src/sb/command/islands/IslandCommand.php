<?php

namespace sb\command\islands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sb\command\islands\subCmd\CreateRoleSubCommand;
use sb\command\islands\subCmd\CreateSubCommand;
use sb\command\islands\subCmd\DeleteRoleSubCommand;
use sb\command\islands\subCmd\DisbandSubCommand;
use sb\command\islands\subCmd\EditRoleSubCommand;
use sb\command\islands\subCmd\GoSubCommand;
use sb\command\islands\subCmd\InviteSubCommand;
use sb\command\islands\subCmd\InfoSubCommand;
use sb\command\islands\subCmd\JoinSubCommand;
use sb\command\islands\subCmd\KickSubCommand;
use sb\command\islands\subCmd\LeaveSubCommand;
use sb\command\islands\subCmd\LevelsSubCommand;
use sb\command\islands\subCmd\LockSubCommand;
use sb\command\islands\subCmd\SetRoleSubCommand;
use sb\command\islands\subCmd\TeleportSubCommand;
use sb\command\islands\subCmd\UpgradeSubCommand;
use sb\command\islands\subCmd\WarpSubCommand;
use sb\command\islands\subCmd\TopSubCommand;
use sb\form\CommandFormButton;
use sb\form\FormHandler;
use sb\permission\utils\IslandPermissions;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class IslandCommand extends BaseCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "island", "Base server command.", ["is"]);
        $this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new CreateRoleSubCommand());
        $this->registerSubCommand(new CreateSubCommand());
		$this->registerSubCommand(new DeleteRoleSubCommand());
		$this->registerSubCommand(new DisbandSubCommand());
		$this->registerSubCommand(new EditRoleSubCommand());
		$this->registerSubCommand(new GoSubCommand());
		$this->registerSubCommand(new InviteSubCommand());
	#	$this->registerSubCommand(new JoinSubCommand());
		$this->registerSubCommand(new KickSubCommand());
		$this->registerSubCommand(new LeaveSubCommand());
        $this->registerSubCommand(new LockSubCommand());
		$this->registerSubCommand(new SetRoleSubCommand());
		$this->registerSubCommand(new TeleportSubCommand());
		$this->registerSubCommand(new UpgradeSubCommand());
        $this->registerSubCommand(new WarpSubCommand());
		$this->registerSubCommand(new LevelsSubCommand());
		$this->registerSubCommand(new InfoSubCommand());
		$this->registerSubCommand(new TopSubCommand());
        $this->registerSubCommand(new JoinSubCommand());
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $user = $sender->getCoreUser();

		$this->getIsland($user->getIsland(), function($island) use ($user, $sender, $args) {
			$islandRole = null;
			$hasIsland = $user->hasIsland();

			if (!is_null($island)) {
				$islandRole = $island->getRole($user);
			}
			$sender->sendForm(FormHandler::commandForm("Island Menu", "Choose an option.", [
				new CommandFormButton("§r§3Create an Island\n§r§8Tap me!", null, !$hasIsland, "is create"),
				new CommandFormButton("§r§3Join an Island\n§r§8Tap me!", null, !$hasIsland, "is join"),
				new CommandFormButton("§r§3Teleport to Your Island\n§r§8Tap me!", null, $hasIsland, "is teleport"),
				new CommandFormButton("§r§3Go to Your Island\n§r§8Tap me", null, $hasIsland, "is go"),
				new CommandFormButton("§r§3Visit an Island\n§r§8Tap me", null, true, "is warp"),
				
				new CommandFormButton(
					"§r§3Invite a Player\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_INVITE),
					"is invite"
				),
				new CommandFormButton(
					"§r§3Kick an Island member\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_KICK),
					"is kick"
				),
				new CommandFormButton(
					"§r§3Set a Island member's role\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_SET_ROLE),
					"is setrole"
				),
				new CommandFormButton("§r§3Island Upgrades\n§r§8Tap me!", null, $hasIsland, "is upgrade"),
				new CommandFormButton("§r§3Island Levels\n§r§8Tap me!", null, $hasIsland, "is levels"),
				new CommandFormButton(
					($island?->isLocked() ?? false) ? "§r§3Un-lock Your Island\n§r§8Tap me!" : "§r§3Lock Your Island\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_LOCK),
					"is lock"
				),
				new CommandFormButton(
					"§r§3Create an Island role\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_EDIT_ROLE),
					"is createrole"
				),
				new CommandFormButton(
					"§r§3Edit an Island role\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_EDIT_ROLE),
					"is editrole"
				),
				new CommandFormButton(
					"§r§3Delete an Island role\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->hasPermission(IslandPermissions::PERMISSION_EDIT_ROLE),
					"is deleterole"),
				new CommandFormButton("§r§3Leave Island\n§r§8Tap me!", null, $hasIsland, "is leave"),
				new CommandFormButton(
					"§r§3Disband Island\n§r§8Tap me!",
					null,
					$hasIsland && $islandRole->getName() == "Owner",
					"is disband"
				),
			]));
		});
    }

}